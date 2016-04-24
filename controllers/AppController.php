<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
//use app\lib\Util;

class AppController extends Controller
{
    public $settings = [];

    public function beforeAction($action)
    {
        $this->settings = Yii::$app->params['settings'];
        $user = Yii::$app->getUser();

        $this->setReturnUrl($action, $user);

        if ( $this->isOffline($action, $user) ) {
            return Yii::$app->getResponse()->redirect(['site/offline']);
        }
        if ( $this->needLogin($action, $user) ) {
            Yii::$app->getSession()->setFlash('accessNG', '您查看的页面需要先登录');
            return Yii::$app->getResponse()->redirect(['site/login']);
        }
//      Yii::$app->getUser()->setReturnUrl(Util::getReferrer());
        return parent::beforeAction($action);
    }

    public function setReturnUrl($action, $user)
    {
        if( $action->controller->id !== 'site' || $action->id !== 'login' ) {
            $user->setReturnUrl(Yii::$app->getRequest()->url);
        }
    }

    public function isOffline($action, $user)
    {
        $actionId = $action->id;
        $controllerId = $action->controller->id;
        $actions = [
            'error',
            'captcha',
            'offline',
            'login',
            'logout',
        ];
        return ( isset($this->settings['offline']) && intval($this->settings['offline']) === 1  //offline
                 && !($controllerId == "site" && in_array($actionId, $actions)) //except actions
//               && !strpos($controllerId, 'admin/')    // except [admin/*]
                 && ($user->getIsGuest() || !$user->getIdentity()->isAdmin()) // is not admin
                );
    }

    public function needLogin($action, $user)
    {
        $actionId = $action->id;
        $controllerId = $action->controller->id;
        $exceptActions = [
            'error',
            'captcha',
            'auth',
            'offline',
            'login',
            'signup',
            'forgot-password',
            'reset-password',
            'verify-email',
            'activate',
            'auth-signup',
            'auth-bind-account',
        ];

        return ( isset($this->settings['access_auth']) && intval($this->settings['access_auth']) === 1  //offline
                 && !($controllerId === "site" && in_array($actionId, $exceptActions)) //except actions
                 && $user->getIsGuest() // is guest
                );
    }
}
