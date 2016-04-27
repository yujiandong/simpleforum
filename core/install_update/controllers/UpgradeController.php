<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
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

    public function actionV112to113()
    {
        if (file_exists(Yii::getAlias('@runtime/upv1.1.2to1.1.3.lock'))) {
            echo '您已升级完成';
            exist;
        }
        $error = false;
        try {
            $this->excuteSql($this->module->basePath . '/data/v1.1.2to1.1.3.sql');
            file_put_contents(Yii::getAlias('@runtime/upv1.1.2to1.1.3.lock'), '');
            return $this->render('completed');
        } catch (\yii\db\Exception $e) {
            $error = '数据库连接出错，请确认数据库连接信息：<br />' . $e->getMessage();
        }
        return $this->render('dbSetting', ['model'=>$model, 'error'=>$error]);
    }

    private function excuteSql($file)
    {
        $db = Yii::$app->getDb();
        $sql = file_get_contents($file);
        $sql = str_replace('simple_', $db->tablePrefix, $sql);
        $db->createCommand($sql)->execute();
    }

}
