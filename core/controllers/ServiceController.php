<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
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
use app\models\EditProfileForm;
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
/*        $me = Yii::$app->getUser()->getIdentity();
        $userInfo = $me->userInfo;
        $userInfo->scenario = UserInfo::SCENARIO_EDIT;

        if ( $userInfo->load(Yii::$app->getRequest()->post()) && $userInfo->save() ) {
            Yii::$app->getSession()->setFlash('EditProfileOK', Yii::t('app', '{attribute} has been changed successfully.', ['attribute'=>Yii::t('app', 'Your account information')]));
        } else {
            Yii::$app->getSession()->setFlash('EditProfileNG', implode('<br />', $userInfo->getFirstErrors()));
        }
*/
        $model = new EditProfileForm();
        $model->load(Yii::$app->getRequest()->post());
        $result = $model->apply();
        Yii::$app->getSession()->setFlash($result[0], $result[1]);

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
            Yii::$app->getSession()->setFlash('activateMailOK', Yii::t('app', 'Email has been sent successfully. Please check your email to activate your account.'));
        } else {
            Yii::$app->getSession()->setFlash('activateMailNG', Yii::t('app', 'An error occured when sending email. Please try later or contact the administrator.'));
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
            $session->setFlash('setAvatarOK', Yii::t('app', 'Avatar has been set successfully. Please refresh the page if it is not shown.'));
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
            $session->setFlash('setCoverOK', Yii::t('app', 'Background picture has been set successfully. Please refresh the page if it is not shown.'));
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
            return ['jquery-upload-file-error'=> Yii::t('app', 'You have no authority to upload attachment.') ];
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
                return ['result'=>0, 'msg'=>Yii::t('app', 'Parameter error')];
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
                return ['result'=>0, 'msg'=>Yii::t('app', 'Parameter error')];
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
                'title' => Yii::t('app', 'You don\'t have enough points.'),
                'status' => 'warning',
                'msg' => Yii::t('app', 'You don\'t have enough points. You can get points from daily bonus service.'),
            ]);
        }
        $model = new SendMsgForm();
        $id = intval($id);
        $sms = null;
        if( $id>0 ) {
            $sms = Notice::findOne($id);
            if ( !$sms ) {
                throw new NotFoundHttpException(Yii::t('app', 'Parameter error'));
            } else {
                $model->username = $sms->source->username;
            }
        } else if (!empty($to)) {
            $model->username = $to;
        }
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            if ( $model->apply() ) {
                Yii::$app->getSession()->setFlash('SendMsgOK', Yii::t('app', 'Message has been sent successfully.'));
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
