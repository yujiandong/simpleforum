<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\lib\Util;

class AppController extends Controller
{
	public $settings = [];

	public function beforeAction($action)
	{
		$this->settings = Yii::$app->params['settings'];
		if ( $this->isOffline($action) ) {
			return Yii::$app->getResponse()->redirect(['site/offline']);
		}
		if ( $this->needLogin($action) ) {
			return Yii::$app->getResponse()->redirect(['site/login']);
		}

		Yii::$app->getUser()->setReturnUrl(Util::getReferrer());
	    return parent::beforeAction($action);
	}

	public function isOffline($action)
	{
		$me = Yii::$app->getUser();
	    $actionId = $action->id;
	    $controllerId = $action->controller->id;
		$actions = [
			'error',
			'captcha',
			'offline',
			'login',
			'logout',
		];
	    return ( isset($this->settings['offline']) && intval($this->settings['offline']) === 1	//offline
				 && !($controllerId == "site" && in_array($actionId, $actions)) //except actions
//				 && !strpos($controllerId, 'admin/')	// except [admin/*]
				 && ($me->getIsGuest() || !$me->getIdentity()->isAdmin()) // is not admin
				);
	}

	public function needLogin($action)
	{
	    $actionId = $action->id;
	    $controllerId = $action->controller->id;
		$actions = [
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

	    return ( isset($this->settings['access_auth']) && intval($this->settings['access_auth']) === 1	//offline
				 && !($controllerId == "site" && in_array($actionId, $actions)) //except actions
				 && Yii::$app->getUser()->getIsGuest() // is guest
				);
	}

}
