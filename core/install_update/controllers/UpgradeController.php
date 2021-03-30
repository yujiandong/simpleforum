<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\install_update\controllers;

use Yii;
use yii\db\Exception;
use app\controllers\AppController;
use app\components\Util;

class UpgradeController extends AppController
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

    public function actionV130to131()
    {
        $this->createConfigFile();
        return $this->render('completed');
    }

    public function actionV115to120()
    {
        if (file_exists(Yii::getAlias('@runtime/upv1.1.5to1.2.0.lock'))) {
            header("Content-Type: text/html; charset=UTF-8");
            echo Yii::t('app/admin', 'Upgrade completed successfully.');
            exit;
        }
        $error = false;
        try {
            @set_time_limit(180);
            $this->excuteSql($this->module->basePath . '/data/v1.1.5to1.2.0.sql');
            file_put_contents(Yii::getAlias('@runtime/upv1.1.5to1.2.0.lock'), '');
            Yii::$app->getCache()->flush();
            return $this->render('completed');
        } catch (Exception $e) {
            header("Content-Type: text/html; charset=UTF-8");
            echo Yii::t('app/admin', 'Error establishing a database connection: Please confirm database settings.') . '<br />' . $e->getMessage();
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

    private function createConfigFile()
    {
        if (!file_exists(Yii::getAlias('@app/config/web.php.default'))){
            echo Yii::t('app', '{attribute} doesn\'t exist.', ['attribute' => Yii::getAlias('@app/config/web.php.default')]);
            exit;
        } else if (!is_writeable(Yii::getAlias('@app/config'))) {
            echo Yii::t('app/admin', '\'{attribute}\' is not writable', ['attribute' => Yii::getAlias('@app/config')]);
            exit;
        } else if (!is_writeable(Yii::getAlias('@app/config/web.php'))) {
            echo Yii::t('app/admin', '\'{attribute}\' is not writable', ['attribute' => Yii::getAlias('@app/config/web.php')]);
            exit;
        }
        $config = file_get_contents(Yii::getAlias('@app/config/web.php.default'));
        $config = str_replace('{cookieValidationKey}', Util::shorturl(Yii::$app->request->hostInfo).'-'.Util::shorturl(Yii::$app->request->hostName), $config);
        return file_put_contents(Yii::getAlias('@app/config/web.php'), $config);
    }

}
