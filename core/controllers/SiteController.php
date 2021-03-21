<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\ServerErrorHttpException;
use yii\web\Cookie;
use app\models\LoginForm;
use app\models\ForgotPasswordForm;
use app\models\ResetPasswordForm;
use app\models\ChangeEmailForm;
use app\models\SignupForm;
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
            'weibo'=>'微博',
            'weixin'=>'微信',
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
                $attr['sourceName'] = empty($sourceName[$source])?$source:$sourceName[$source];
                $session = Yii::$app->getSession();
                $session->set('authInfo', $attr);
                return $this->redirect(['auth-bind-account']);

            }
        } else { // user already signed in
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

    public function actionLanguage()
    {
        $language = Yii::$app->getRequest()->get('language');
        if($language) {
            //Yii::$app->language = $language;
            $languageCookie = new Cookie([
                'name' => 'language',
                'value' => $language,
                'expire' => time() + 60 * 60 * 24 * 30, // 30 days
            ]);
            Yii::$app->getResponse()->getCookies()->add($languageCookie);
        }
        //$this->redirect(['topic/index']);
        return $this->goBack();
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
            return $this->render('@app/views/common/info', ['title'=>Yii::t('app', 'Registration Closed'), 'status'=>'success', 'msg'=>Yii::t('app', 'Please use {url} to sign in.', ['url' => \yii\helpers\Html::a(Yii::t('app', 'Third-Party Accounts'), ['site/login'])])]);
        }
        $model = new SignupForm();
        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    if ($user->status == User::STATUS_INACTIVE) {
                        return $this->render('@app/views/common/info', ['title'=>Yii::t('app', 'Thank You For Your Registration'), 'status'=>'success', 'msg'=>Yii::t('app', 'An email has been sent to your email address containing an activation link. Please click on the link to activate your account. If you do not receive the email within a few minutes, please check your spam folder.')]);
                    } else if ($user->status == User::STATUS_ADMIN_VERIFY) {
                        return $this->render('@app/views/common/info', ['title'=>Yii::t('app', 'Thank You For Your Registration'), 'status'=>'success', 'msg'=>Yii::t('app', 'Please wait for the admin approval.')]);
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
            return $this->render('@app/views/common/info', ['title'=>Yii::t('app', 'Reset Password'), 'status'=>'warning', 'msg'=>$e->getMessage()]);
        }
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            try {
                $model->apply();
                return $this->render('@app/views/common/info', ['title'=>Yii::t('app', 'Reset Password'), 'status'=>'success', 'msg'=>Yii::t('app', 'An email has been sent to your email address containing a verification link. Please click on the link to reset your password. If you do not receive the email within a few minutes, please check your spam folder.')]);
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
            return $this->render('@app/views/common/info', ['title'=>Yii::t('app', 'Password Reset Failed'), 'status'=>'warning', 'msg'=>$e->getMessage()]);
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->resetPassword()) {
            return $this->render('@app/views/common/info', ['title'=>Yii::t('app', 'Password Reset Successfully'), 'status'=>'success', 'msg'=>Yii::t('app', 'You can use new password to sign in.')]);
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
            return $this->render('@app/views/common/info', ['title'=>Yii::t('app', 'Email Change Failed'), 'status'=>'warning', 'msg'=>$e->getMessage()]);
        }

        if ( Yii::$app->getRequest()->getIsPost() ) {
            $token->status = Token::STATUS_USED;
            $token->save(false);

            $model = new ChangeEmailForm(['scenario' => ChangeEmailForm::SCENARIO_VERIFY_EMAIL, 'email'=>$token->ext]);
            if ( !$model->validate() ) {
                 $result = ['title'=>Yii::t('app', 'Email Change Failed'), 'status'=>'warning', 'msg'=>Yii::t('app', '{attribute} is already in use.', ['attribute' => Yii::t('app', 'Email') . '(' . $token->ext . ')'])];
            } else {
                $user = $token->user;
                $user->email = $token->ext;
                $user->save(false);

                $result = ['title'=>Yii::t('app', 'Email Change Successfully'), 'status'=>'success', 'msg'=>Yii::t('app', 'Your email address has been changed.')];
            }
        } else {
                $result = [
                    'title'=>Yii::t('app', 'Email Change Verification'),
                    'status'=>'info', 
                    'msg'=>Yii::t('app', 'Please click to {url} .', ['url' => Html::a('<i class="fa fa-link" aria-hidden="true"></i> '. Yii::t('app', 'verify your new email'), Yii::$app->getRequest()->url, [
                        'title' => Yii::t('app', 'Email Change Verification'),
                        'data' => [
                            'method' => 'post',
                        ]])
                    ])
                ];
        }
        return $this->render('@app/views/common/info', $result);
    }

    public function actionActivate($token)
    {
        try {
            $token = Token::findByToken($token, Token::TYPE_REG);
        } catch (InvalidParamException $e) {
            return $this->render('@app/views/common/info', ['title'=>Yii::t('app', 'Account Activation Failed'), 'status'=>'warning', 'msg'=>$e->getMessage()]);
        }

        if ( Yii::$app->getRequest()->getIsPost() ) {
            $user = $token->user;

            $token->status = Token::STATUS_USED;
            $token->save(false);

            if ( !empty($token->ext) && $user->email !== $token->ext && User::findOne(['email'=>$token->ext]) ) {
                return $this->render('@app/views/common/info', ['title'=>Yii::t('app', 'Account Activation Failed'), 'status'=>'warning', 'msg'=> Yii::t('app', '{attribute} is already in use.', ['attribute' => Yii::t('app', 'Email') . '(' . $token->ext . ')'])]);
            }
            if (intval($this->settings['admin_verify']) === 1) {
                $user->status = User::STATUS_ADMIN_VERIFY;
                $result = ['title'=>Yii::t('app', 'Email Verification Succeeded'), 'status'=>'success', 'msg'=>Yii::t('app', 'Your email address is now verified. Please wait for the admin approval.')];
            } else {
                $user->status = User::STATUS_ACTIVE;
                $result = ['title'=>Yii::t('app', 'Account Activation Succeeded'), 'status'=>'success', 'msg'=>Yii::t('app', 'Your account has been activated successfully. You can now {url}.', ['url' => \yii\helpers\Html::a(Yii::t('app', 'Sign in'), ['site/login'])])];
            }
            $user->email = $token->ext;
            $user->save(false);

        } else {
                $result = [
                    'title'=>Yii::t('app', 'Account Activation'),
                    'status'=>'info', 
                    'msg'=>Yii::t('app', 'Please click to {url} .', ['url' => Html::a('<i class="fa fa-link" aria-hidden="true"></i>' . Yii::t('app', 'activate your account'), Yii::$app->getRequest()->url, [
                        'title' => Yii::t('app', 'Account Activation'),
                        'data' => [
                            'method' => 'post',
                        ]])
                    ])
                ];
        }
        return $this->render('@app/views/common/info', $result);
    }

}
