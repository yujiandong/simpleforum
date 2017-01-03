<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\install_update\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\ServerErrorHttpException;
use yii\web\ForbiddenHttpException;
use app\install_update\lib\RequirementChecker;
use app\install_update\models\DatabaseForm;
use app\install_update\models\AdminSignupForm;

class UpgradeController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionV115to120()
    {
        if (file_exists(Yii::getAlias('@runtime/upv1.1.5to1.2.0.lock'))) {
            header("Content-Type: text/html; charset=UTF-8");
            echo '您已升级完成';
            exit;
        }
        $error = false;
        try {
            @set_time_limit(180);
            $this->excuteSql($this->module->basePath . '/data/v1.1.5to1.2.0.sql');
            file_put_contents(Yii::getAlias('@runtime/upv1.1.5to1.2.0.lock'), '');
            Yii::$app->getCache()->flush();
            return $this->render('completed');
        } catch (\yii\db\Exception $e) {
            header("Content-Type: text/html; charset=UTF-8");
            echo '数据库连接出错，请确认数据库连接信息：<br />' . $e->getMessage();
            exit;
        }
    }

    private function excuteSql($file)
    {
        $db = Yii::$app->getDb();
        $sql = file_get_contents($file);
        $sql = str_replace('simple_', $db->tablePrefix, $sql);
        $db->createCommand($sql)->execute();
    }

}
