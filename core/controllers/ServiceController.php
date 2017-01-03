<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\UserInfo;
use app\models\Token;
use app\models\UploadForm;
use app\models\ChangePasswordForm;
use app\models\ChangeEmailForm;
use app\models\Notice;
use app\models\Auth;
use app\models\History;
use app\models\BuyInviteCodeForm;
use app\models\SendMsgForm;
use app\models\Favorite;
use app\lib\Util;

class ServiceController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'avatar' => ['post'],
//                    'cover' => ['post'],
                    'upload' => ['post'],
                    'edit-profile' => ['post'],
                    'change-email' => ['post'],
                    'change-password' => ['post'],
                    'unfavorite' => ['post'],
                    'good' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
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

        return $this->redirect(['my/settings', '#'=>'info']);
    }

    public function actionChangeEmail()
    {
        $model = new ChangeEmailForm();
        $model->load(Yii::$app->getRequest()->post());
        $result = $model->apply();
        Yii::$app->getSession()->setFlash($result[0], $result[1]);

//      return $this->goBack();
        return $this->redirect(['my/settings', '#'=>'email']);
    }

    public function actionUnbindAccount($source)
    {
        $model = Auth::find()->where(['source'=>$source, 'user_id'=>Yii::$app->getUser()->id])->limit(1)->one();
        $model->delete();
        return $this->redirect(['my/settings', '#'=>'auth']);
    }

    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();
        $model->load(Yii::$app->getRequest()->post());
        $result = $model->apply();
        Yii::$app->getSession()->setFlash($result[0], $result[1]);

//      return $this->goBack();
        return $this->redirect(['my/settings', '#'=>'password']);
    }

    public function actionSendActivateMail()
    {
        if (Token::sendActivateMail(Yii::$app->getUser()->getIdentity())) {
            Yii::$app->getSession()->setFlash('activateMailOK', '邮件发送成功，请进邮箱点击激活链接');
        } else {
            Yii::$app->getSession()->setFlash('activateMailNG', '邮件发送失败');
        }

//      return $this->goBack();
        return $this->redirect(['my/settings']);
    }

    public function actionAvatar()
    {
        $session = Yii::$app->getSession();
        $me = Yii::$app->getUser()->getIdentity();

        $model = new UploadForm(Yii::$container->get('avatarUploader'), ['scenario' => UploadForm::SCENARIO_AVATAR]);
        $model->file = UploadedFile::getInstance($model, 'file');

        $result = $model->uploadAvatar($me->id);
        if ( $result ) {
            $me->avatar = $result;
            $me->save(false);
            $session->setFlash('setAvatarOK', '头像设定成功，显示可能有延迟，请刷新。');
        } else {
            $session->setFlash('setAvatarNG', implode('<br />', $model->getFirstErrors()));
        }

        return $this->redirect(['my/settings', '#'=>'avatar']);
    }

/*    public function actionCover()
    {
        $session = Yii::$app->getSession();
        $me = Yii::$app->getUser()->getIdentity();

        $model = new UploadForm(Yii::$container->get('avatarUploader'), ['scenario' => UploadForm::SCENARIO_AVATAR]);
        $model->file = UploadedFile::getInstance($model, 'file');

        $result = $model->uploadAvatar($me->id, 'coverSizes');
        if ( $result ) {
            $me->userInfo->cover = $result;
            $me->userInfo->save(false);
            $session->setFlash('setCoverOK', '用户卡背景图片上传成功，显示可能有延迟，请刷新。');
        } else {
            $session->setFlash('setCoverNG', implode('<br />', $model->getFirstErrors()));
        }

        return $this->redirect(['my/settings', '#'=>'cover']);
    }
*/
    public function actionUpload()
    {
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;

        $me = Yii::$app->getUser()->getIdentity();

        if( !$me->canUpload($this->settings) ) {
            return ['jquery-upload-file-error'=> '您没有权限上传附件。' ];
        }

        $model = new UploadForm(Yii::$container->get('fileUploader'), ['scenario' => UploadForm::SCENARIO_UPLOAD]);
        $model->files = UploadedFile::getInstances($model, 'files');

        $result = $model->upload($me->id);
        if ( $result ) {
            return $result;
        } else {
            return ['jquery-upload-file-error'=> implode('<br />', $model->getFirstErrors()) ];
        }

    }

    public function actionFavorite()
    {
        $types = [
            'node' => Favorite::TYPE_NODE,
            'topic' => Favorite::TYPE_TOPIC,
            'user' => Favorite::TYPE_USER,
        ];
        $req = Yii::$app->getRequest();
        if ($req->getIsAjax()) {
            $data = $req->post();
            if ( !isset($types[$data['type']]) ) {
                return ['result'=>0, 'msg'=>'参数不正确'];
            }

            Favorite::add([
                'type'=>$types[$data['type']],
                'source_id'=>Yii::$app->getUser()->id,
                'target_id'=>$data['id'],
            ]);
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            return ['result'=>1];
        }

//        return $this->goBack();
    }

    public function actionUnfavorite()
    {
        $types = [
            'node' => Favorite::TYPE_NODE,
            'topic' => Favorite::TYPE_TOPIC,
            'user' => Favorite::TYPE_USER,
        ];
        $req = Yii::$app->getRequest();
        if ($req->getIsAjax()) {
            $data = $req->post();
            if ( !isset($types[$data['type']]) ) {
                return ['result'=>0, 'msg'=>'参数不正确'];
            }

            Favorite::cancel([
                'type'=>$types[$data['type']],
                'source_id'=>Yii::$app->getUser()->id,
                'target_id'=>$data['id'],
            ]);
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            return ['result'=>1];
        }


//        return $this->goBack();
    }

    public function actionSignin()
    {
        if ( Yii::$app->getRequest()->getIsPost() ) {
            Yii::$app->getUser()->getIdentity()->signin();
        }
        return $this->render('signin');

    }

    public function actionBuyInviteCode()
    {
        $model = new BuyInviteCodeForm();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $model->apply();
            return $this->redirect(['my/invite-codes']);
        }
        return $this->render('buyInviteCode', [
            'model' => $model,
        ]);

    }

    public function actionSms($id=0, $to='')
    {
        $me = Yii::$app->getUser()->getIdentity();
        if( !$me->checkActionCost('sendMsg') ) {
            return $this->render('@app/views/common/info', [
                'title' => '您的积分不足',
                'status' => 'warning',
                'msg' => '您的积分不足，不能发送消息。每日签到可以获取10-50不等积分。',
            ]);
        }
        $model = new SendMsgForm();
        $id = intval($id);
        $sms = null;
        if( $id>0 ) {
            $sms = Notice::findOne($id);
            if ( !$sms ) {
                throw new NotFoundHttpException('参数不正确。');
            } else {
                $model->username = $sms->source->username;
            }
        } else if (!empty($to)) {
            $model->username = $to;
        }
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            if ( $model->apply() ) {
                Yii::$app->getSession()->setFlash('SendMsgOK', '短消息发送成功。');
                $model = new SendMsgForm();
            }
        }
        return $this->render('sms', [
            'model' => $model,
            'sms' => $sms,
        ]);

    }

    public function actionGood()
    {
        $req = Yii::$app->getRequest();
        if ($req->getIsAjax()) {
            $data = $req->post();
            $me = Yii::$app->getUser()->getIdentity();
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            return $me->good($data['type'], intval($data['id']), intval($data['thanks']));
        }
    }

}
