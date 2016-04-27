<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\UploadedFile;
use app\models\User;
use app\models\UserInfo;
use app\models\Notice;
use app\models\Topic;
use app\models\Comment;
use app\models\Token;
use app\models\UploadForm;
use app\models\ChangePasswordForm;
use app\models\ChangeEmailForm;
use app\models\Auth;
use app\lib\Util;

class UserController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'avatar' => ['post'],
                    'upload' => ['post'],
                    'edit-profile' => ['post'],
                    'change-email' => ['post'],
                    'change-password' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['setting', 'upload', 'avatar', 'notifications', 'edit-profile', 'change-password', 'change-email', 'send-activate-mail', 'unbind-account'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['setting', 'upload', 'avatar', 'notifications', 'edit-profile', 'change-password', 'change-email', 'send-activate-mail', 'unbind-account'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionNotifications()
    {
		$myId = Yii::$app->getUser()->id;
		Notice::updateAll([
			'updated_at'=>time(),
			'status'=> 1,
		], ['status'=> 0, 'target_id'=>$myId]);

		$query = Notice::find()->where(['target_id'=>$myId]);
	    $countQuery = clone $query;
	    $pages = new Pagination(['totalCount' => $countQuery->count('id')]);
	    $notices = $query->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
			->with(['source', 'topic'])
	        ->limit($pages->limit)
			->asArray()
	        ->all();
	    return $this->render('notifications', [
	         'notices' => $notices,
	         'pages' => $pages,
	    ]);
    }

    public function actionView($username)
    {
		$model = $this->findUserModel($username, ['userInfo', 'topics.node', 'topics.lastReply', 'comments.topic.author']);
	    return $this->render('view', [
	         'user' => $model,
	    ]);
    }

    public function actionTopics($username)
    {
		$user = $this->findUserModel($username, ['userInfo']);
	    $pages = new Pagination(['totalCount' => $user['userInfo']['topic_count'], 'pageSize' => $this->settings['list_pagesize'], 'pageParam'=>'p']);
	    $topics = Topic::find()->select('id')->where(['user_id' => $user['id']])
			->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
			->with(['topic.node', 'topic.lastReply'])
	        ->limit($pages->limit)
			->asArray()
	        ->all();

	    return $this->render('topics', [
			 'user' => $user,
	         'topics' => $topics,
	         'pages' => $pages,
	    ]);
    }

    public function actionComments($username)
    {
		$user = $this->findUserModel($username, ['userInfo']);
	    $pages = new Pagination(['totalCount' => $user['userInfo']['comment_count'], 'pageSize' => $this->settings['list_pagesize'], 'pageParam'=>'p']);
	    $comments = Comment::find()->select(['id', 'created_at', 'topic_id', 'content', 'invisible'])->where(['user_id' => $user['id']])
			->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
			->with(['topic.author'])
	        ->limit($pages->limit)
			->asArray()
	        ->all();

	    return $this->render('comments', [
			 'user' => $user,
	         'comments' => $comments,
	         'pages' => $pages,
	    ]);
    }

	public function actionSetting()
	{
        return $this->render('setting');
	}

    public function actionEditProfile()
    {
		$me = Yii::$app->getUser()->getIdentity();
		$userInfo = $me->userInfo;
		$userInfo->scenario = UserInfo::SCENARIO_EDIT;

		if ( $userInfo->load(Yii::$app->getRequest()->post()) && $userInfo->save() ) {
			Yii::$app->getSession()->setFlash('EditProfileOK', '您的会员信息修改成功。');
		} else {
			Yii::$app->getSession()->setFlash('EditProfileNG', implode('<br />', $userInfo->getFirstErrors()));
		}

        return $this->redirect(['user/setting', '#'=>'info']);
    }

    public function actionChangeEmail()
    {
        $model = new ChangeEmailForm();
		$model->load(Yii::$app->getRequest()->post());
		$result = $model->apply();
		Yii::$app->getSession()->setFlash($result[0], $result[1]);

//		return $this->goBack();
        return $this->redirect(['user/setting', '#'=>'email']);
    }

    public function actionUnbindAccount($source)
    {
        $model = Auth::find()->where(['source'=>$source, 'user_id'=>Yii::$app->getUser()->id])->limit(1)->one();
		$model->delete();
        return $this->redirect(['user/setting', '#'=>'auth']);
    }

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();
		$model->load(Yii::$app->getRequest()->post());
		$result = $model->apply();
		Yii::$app->getSession()->setFlash($result[0], $result[1]);

//		return $this->goBack();
        return $this->redirect(['user/setting', '#'=>'password']);
    }

    public function actionSendActivateMail()
    {
		if (Token::sendActivateMail(Yii::$app->getUser()->getIdentity())) {
			Yii::$app->getSession()->setFlash('activateMailOK', '邮件发送成功，请进邮箱点击激活链接');
		} else {
			Yii::$app->getSession()->setFlash('activateMailNG', '邮件发送失败');
		}

//		return $this->goBack();
        return $this->redirect(['user/setting']);
    }

    public function actionAvatar()
    {
		$session = Yii::$app->getSession();
		$me = Yii::$app->getUser()->getIdentity();

        $model = new UploadForm(['scenario' => UploadForm::SCENARIO_AVATAR]);
        $model->file = UploadedFile::getInstance($model, 'file');

		$result = $model->uploadAvatar($me->id);
		if ( $result ) {
			$me->avatar = $result;
			$me->save(false);
            $session->setFlash('setAvatarOK', '头像设定成功，显示可能有延迟，请刷新。');
		} else {
            $session->setFlash('setAvatarNG', implode('<br />', $model->getFirstErrors()));
		}

        return $this->redirect(['user/setting', '#'=>'avatar']);
    }

    public function actionUpload()
    {
	    \Yii::$app->getResponse()->format = \yii\web\Response::FORMAT_JSON;

		$session = Yii::$app->getSession();
		$me = Yii::$app->getUser()->getIdentity();

		if( !$me->canUpload($this->settings) ) {
			return ['jquery-upload-file-error'=> '您没有权限上传附件。' ];
		}

        $model = new UploadForm(['scenario' => UploadForm::SCENARIO_UPLOAD]);
        $model->files = UploadedFile::getInstances($model, 'files');

		$result = $model->upload($me->id);
		if ( $result ) {
			return $result;
		} else {
			return ['jquery-upload-file-error'=> implode('<br />', $model->getFirstErrors()) ];
		}

    }

    protected function findUserModel($username, $with=null)
    {
		$model = User::find()->select(['id', 'created_at', 'status', 'username', 'avatar'])->where(['username'=>$username]);
		if ( !empty($with) ) {
			$model = $model->with($with);
		}
		$model = $model->asArray()->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('未找到['.$username.']的用户');
        }
    }

}
