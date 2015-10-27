<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\imagine\Image;
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
use app\lib\Util;

class UserController extends AppController
{
	private $resizes = [
		'large'=>'73x73',
		'normal'=>'48x48',
		'small'=>'24x24',
	];

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'avatar' => ['post'],
                    'edit-profile' => ['post'],
                    'change-email' => ['post'],
                    'change-password' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['setting', 'avatar', 'notifications', 'edit-profile', 'change-password', 'change-email', 'send-activate-mail'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['setting', 'avatar', 'notifications', 'edit-profile', 'change-password', 'change-email', 'send-activate-mail'],
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
	    $comments = Comment::find()->select(['id', 'created_at', 'topic_id', 'content'])->where(['user_id' => $user['id']])
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
        $model = new UploadForm();
		$suffix = 'png';

		$me = Yii::$app->getUser()->getIdentity();

        $model->file = UploadedFile::getInstance($model, 'file');

        if ($model->file && $model->validate()) {
	        $name = $me->id;
			$myId = strtolower(Util::shorturl($me->id));
			$savePath = 'avatar/'.substr($myId,0,1).'/'.substr($myId,1,1);
			$avatar = $savePath. '/'.$name . '_{size}.' . $suffix . '?m='.time();
			$this->resizeAvator( $this->resizes, $model->file->tempName, $savePath, $name, $suffix);
			$me->avatar = $avatar;
			$me->save(false);
            $session->setFlash('setAvatarOK', '头像设定成功，显示可能有延迟，请刷新。');
        } else {
            $session->setFlash('setAvatarNG', '头像设定失败');
		}

        return $this->redirect(['user/setting', '#'=>'avatar']);
    }

	private function resizeAvator($resizes, $srcFile, $savePath, $name, $suffix='png')
	{
		@mkdir($savePath, 0755, true);
		foreach($resizes as $key=>$resize) {
			list($width, $height) = explode('x', $resize);
			Image::thumbnail($srcFile, $width, $height)->save($savePath. '/' . $name . '_' . $key . '.' . $suffix);
		}
		return true;
	}

/*
    private function saveTemporaryImage($filename, $name, $size)
    {
        list($width, $height) = explode('x', $size);

        $file = Image::getImagine()->open($filename);

        if ($file->getSize()->getWidth() < $width) {
            $file->resize($file->getSize()->widen($width));
        }

        if ($file->getSize()->getHeight() < $height) {
            $file->resize($file->getSize()->heighten($height));
        }

        $file->thumbnail(new \Imagine\Image\Box::Box($width, $height), \Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND)
            ->save('avatar/' . $name);

        return $name;
    }
*/

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
