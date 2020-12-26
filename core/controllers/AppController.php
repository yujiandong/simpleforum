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
            Yii::$app->getSession()->setFlash('accessNG', 'You need to sign in to view this page.');
            return Yii::$app->getResponse()->redirect(['site/login']);
        }
//      Yii::$app->getUser()->setReturnUrl(Util::getReferrer());
        return parent::beforeAction($action);
    }

    public function setReturnUrl($action, $user)
    {
        $exceptAllActions = [
            'delete',
        ];
        $exceptActions = [
            'site/error',
            'site/captcha',
            'site/auth',
            'site/offline',
            'site/login',
            'site/signup',
            'site/auth-signup',
            'site/forgot-password',
            'site/reset-password',
            'site/verify-email',
            'site/activate',
            'site/auth-bind-account',
	    'site/language',
            'admin/user/activate',
            'admin/user/reset-password',
            'service/favorite',
            'service/unfavorite',
            'service/good',
            'service/avatar',
//            'service/cover',
//            'user/ajax-view',
        ];
        if( !Yii::$app->getRequest()->getIsAjax()
                && !in_array( $action->id, $exceptAllActions) 
                && !in_array( $action->controller->id.'/'.$action->id, $exceptActions) ) {
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
            'language',
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
            'language',
        ];

        return ( isset($this->settings['access_auth']) && intval($this->settings['access_auth']) === 1  //offline
                 && !($controllerId === "site" && in_array($actionId, $exceptActions)) //except actions
                 && $user->getIsGuest() // is guest
                );
    }
}
