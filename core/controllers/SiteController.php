<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Html;
use app\models\LoginForm;
use app\models\ForgotPasswordForm;
use app\models\ResetPasswordForm;
use app\models\ChangeEmailForm;
use app\models\SignupForm;
use app\models\ContactForm;
use app\models\Auth;
use app\models\User;
use app\models\Token;

/**
 * Site controller
 */
class SiteController extends AppController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'forgot-password', 'reset-password'],
                'rules' => [
                    [
                        'actions' => ['signup', 'forgot-password', 'reset-password'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'height' => 40,
                'maxLength' => 5,
                'minLength' => 4,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        $sourceName = [
            'qq'=>'qq',
            'weibo'=>'微博',
            'weixin'=>'微信',
            'baidu'=>'百度',
        ];
        $me = Yii::$app->getUser();

        $source = $client->getId();
        $attr = $client->getUserAttributes();

        $auth = Auth::findOne([
            'source' => $source,
            'source_id' => (string)$attr['id'],
        ]);

        if ($me->getIsGuest()) {
            if ($auth) { // login
                $user = $auth->user;
                $me->login($user);
            } else { // signup
                $attr['source'] = $source;
//              $attr['sourceName'] = $client->defaultName();
                $attr['sourceName'] = $sourceName[$source];
                $session = Yii::$app->getSession();
                $session->set('authInfo', $attr);
                return $this->redirect(['auth-bind-account']);

            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $auth = new Auth([
                    'user_id' => Yii::$app->getUser()->id,
                    'source' => $source,
                    'source_id' => (string)$attr['id'],
                ]);
                $auth->save();
                if(Yii::$app->getRequest()->get('action') === 'bind') {
                    $this->redirect(['my/settings', '#'=>'auth']);
                }
            }
        }
    }

    public function actionAuthSignup()
    {
        $session = Yii::$app->getSession();
        if( !$session->has('authInfo') ) {
            return $this->redirect(['login']);
        }
        $attr = $session->get('authInfo');

        $model = new SignupForm(['action' => SignupForm::ACTION_AUTH_SIGNUP]);
        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($user = $model->signup()) {
                $auth = new Auth([
                    'user_id' => $user->id,
                    'source' => (string)$attr['source'],
                    'source_id' => (string)$attr['id'],
                ]);
                if ($auth->save()) {
                    $session->remove('authInfo');
                } else {
                    throw new ServerErrorHttpException(implode('<br />', $auth->getFirstErrors()));
                }
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
            'authInfo' => $attr,
        ]);
    }

    public function actionAuthBindAccount()
    {
        if (! Yii::$app->getUser()->getIsGuest()) {
            return $this->goHome();
        }

        $session = Yii::$app->getSession();
        if( !$session->has('authInfo') ) {
            return $this->redirect(['login']);
        }
        $attr = $session->get('authInfo');

        $model = new LoginForm(['scenario' => LoginForm::SCENARIO_BIND]);
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            $auth = new Auth([
                'user_id' => Yii::$app->getUser()->id,
                'source' => (string)$attr['source'],
                'source_id' => (string)$attr['id'],
            ]);
            if ($auth->save()) {
                $session->remove('authInfo');
            } else {
                throw new ServerErrorHttpException(implode('<br />', $auth->getFirstErrors()));
            }
            return $this->goHome();
        } else {
            return $this->render('authBindAccount', [
                'model' => $model,
                'authInfo' => $attr,
            ]);
        }

    }

    public function actionLogin()
    {
        if (! Yii::$app->getUser()->getIsGuest()) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionOffline()
    {
        return $this->render('offline');
    }

    public function actionLogout()
    {
        Yii::$app->getUser()->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        if ( intval($this->settings['close_register']) === 1) {
            return $this->render('opResult', ['title'=>'用户注册已关闭', 'status'=>'success', 'msg'=>'请使用'. \yii\helpers\Html::a('第三方帐号登录', ['site/login'])]);
        }
        $model = new SignupForm();
        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    if ($user->status == User::STATUS_INACTIVE) {
                        return $this->render('opResult', ['title'=>'帐号注册成功', 'status'=>'success', 'msg'=>'感谢您的注册，激活邮件已发送到您的注册邮箱。请去点击激活。']);
                    } else if ($user->status == User::STATUS_ADMIN_VERIFY) {
                        return $this->render('opResult', ['title'=>'帐号注册成功', 'status'=>'success', 'msg'=>'感谢您的注册，请等待管理员确认。']);
                    } else {
                        return $this->goHome();
                    }
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionForgotPassword()
    {
        try {
            $model = new ForgotPasswordForm();
        } catch (InvalidParamException $e) {
            return $this->render('opResult', ['title'=>'密码重置申请', 'status'=>'warning', 'msg'=>$e->getMessage()]);
        }
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            try {
                $model->apply();
                return $this->render('opResult', ['title'=>'密码重置申请', 'status'=>'success', 'msg'=>'密码重置链接已发送到您的邮箱，请进邮箱确认。']);
            } catch (InvalidParamException $e) {
                Yii::$app->getSession()->setFlash('sendPwdNG', $e->getMessage());
            }
        }

        return $this->render('forgotPassword', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            return $this->render('opResult', ['title'=>'密码重置失败', 'status'=>'warning', 'msg'=>$e->getMessage()]);
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->resetPassword()) {
            return $this->render('opResult', ['title'=>'密码重置成功', 'status'=>'success', 'msg'=>'新密码已生效']);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionVerifyEmail($token)
    {
        try {
            $token = Token::findByToken($token, Token::TYPE_EMAIL);
        } catch (InvalidParamException $e) {
            return $this->render('opResult', ['title'=>'邮箱绑定失败', 'status'=>'warning', 'msg'=>$e->getMessage()]);
        }

        if ( Yii::$app->getRequest()->getIsPost() ) {
            $token->status = Token::STATUS_USED;
            $token->save(false);

            $model = new ChangeEmailForm(['scenario' => ChangeEmailForm::SCENARIO_VERIFY_EMAIL, 'email'=>$token->ext]);
            if ( !$model->validate() ) {
                 $result = ['title'=>'邮箱绑定失败', 'status'=>'warning', 'msg'=>'您申请绑定的邮箱['.$token->ext.']已被注册使用'];
            } else {

                $user = $token->user;
                $user->email = $token->ext;
                $user->save(false);

                $result = ['title'=>'邮箱绑定成功', 'status'=>'success', 'msg'=>'新邮件绑定成功'];
            }
        } else {
                $result = [
                    'title'=>'确认绑定邮箱',
                    'status'=>'info', 
                    'msg'=>'请点击-> '.Html::a('确认您所绑定的邮箱', Yii::$app->getRequest()->url, [
                        'title' => '确认绑定邮箱',
                        'data' => [
                            'method' => 'post',
                        ]
                    ])
                ];
        }
        return $this->render('opResult', $result);
    }

    public function actionActivate($token)
    {
        try {
            $token = Token::findByToken($token, Token::TYPE_REG);
        } catch (InvalidParamException $e) {
            return $this->render('opResult', ['title'=>'帐号激活失败', 'status'=>'warning', 'msg'=>$e->getMessage()]);
        }

        if ( Yii::$app->getRequest()->getIsPost() ) {
            $user = $token->user;

            $token->status = Token::STATUS_USED;
            $token->save(false);

            if ( !empty($token->ext) && $user->email !== $token->ext && User::findOne(['email'=>$token->ext]) ) {
                return $this->render('opResult', ['title'=>'帐号激活失败', 'status'=>'warning', 'msg'=>'申请绑定邮箱['.$token->ext.']已被注册使用']);
            }
            if (intval($this->settings['admin_verify']) === 1) {
                $user->status = User::STATUS_ADMIN_VERIFY;
                $result = ['title'=>'注册邮箱确认成功', 'status'=>'success', 'msg'=>'注册邮箱确认成功，请等待管理员验证。'];
            } else {
                $user->status = User::STATUS_ACTIVE;
                $result = ['title'=>'帐号激活成功', 'status'=>'success', 'msg'=>'帐号激活成功，现在可以 '. \yii\helpers\Html::a('登录', ['site/login']) .' 发贴和回帖了。'];
            }
            $user->email = $token->ext;
            $user->save(false);

        } else {
                $result = [
                    'title'=>'激活会员帐号',
                    'status'=>'info', 
                    'msg'=>'请点击-> '.Html::a('激活会员帐号', Yii::$app->getRequest()->url, [
                        'title' => '激活帐号',
                        'data' => [
                            'method' => 'post',
                        ]
                    ])
                ];
        }
        return $this->render('opResult', $result);
    }

}
