<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers\admin;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class CommonController extends \app\controllers\AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            $me = Yii::$app->getUser();
                            return ( !$me->getIsGuest() && $me->getIdentity()->isAdmin() );
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new \yii\web\NotFoundHttpException('该网页不存在');
                }
            ],
        ];
    }

    public function isOffline($action, $user)
    {
        return false;
    }

}
