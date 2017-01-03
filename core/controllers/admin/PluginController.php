<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers\admin;

use Yii;
use yii\base\Model;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use app\models\admin\Plugin;
use app\models\Setting;
use app\components\Util;

class PluginController extends CommonController
{
    private static $installed;
    private static $installable;

    public function actionIndex()
    {
        self::getInstalledPlugins();
        return $this->render('index', [
            'plugins' => self::$installed
        ]);

    }

    public function actionInstallable()
    {
        self::getInstalledPlugins();
        self::getInstallablePlugins();
        return $this->render('installable', [
            'plugins' => self::$installable
        ]);
    }

    public function actionView($pid)
    {
        $plugin = Plugin::find()->where(['pid'=>$pid])->asArray()->one();
        if( $plugin ) {
            $plugin['installed'] = true;
        } else {
            $plugin = self::getInstallablePlugin($pid);
        }
        return $this->render('view', [
            'plugin' => $plugin
        ]);
    }

    public function actionInstall($pid)
    {
        if( Plugin::findOne(['pid'=>$pid]) ) {
            throw new NotFoundHttpException('插件['.$pid.']已经安装，不能重复安装。');
        }
        $plugin = self::getInstallablePlugin($pid);
        if ( !is_callable([$plugin['handlerClass'], 'install']) ) {
            throw new NotFoundHttpException('插件['.$pid.']不存在install方法。');
        }
        if ( !$plugin['handlerClass']::install() ) {
            throw new NotFoundHttpException('插件['.$pid.']安装出错。');
        }
        $plugin += ['config'=>'', 'events'=>'', 'settings'=>''];
        $flgConfig = false;
        if( !empty($plugin['config']) ) {
            $flgConfig = true;
            $plugin['settings'] = json_encode(ArrayHelper::map($plugin['config'], 'key', 'value'));
            $plugin['config'] = json_encode($plugin['config']);
        }
        if( !empty($plugin['events']) ) {
            $plugin['events'] = json_encode($plugin['events']);
        }
        $model = new Plugin();
        if( $model->load(['Plugin'=>$plugin]) && $model->save() ) {
            self::createPluginsFile();
            if ($flgConfig) {
                return $this->redirect(['settings', 'pid'=>$pid]);
            } else {
                return $this->redirect(['index']);
            }
        } else {
            throw new NotFoundHttpException('插件['.$pid.']安装出错: '.$model->getFirstErrors());
        }
        return $this->render('view', [
            'plugin' => $plugin
        ]);
    }

    public function actionUninstall($pid)
    {
        $plugin = $this->findModel($pid);
        $handlerClass = 'app\plugins\\'.$plugin->pid.'\\'.$plugin->pid;
        if ( !is_callable([$handlerClass, 'uninstall']) ) {
            throw new NotFoundHttpException('插件['.$pid.']不存在uninstall方法。');
        }
        if ( !$handlerClass::uninstall() ) {
            throw new NotFoundHttpException('插件['.$pid.']卸载出错。');
        }
        $plugin->delete();
        self::createPluginsFile();
        return $this->redirect(['index']);
    }

    public function actionSettings($pid)
    {
        $plugin = $this->findModel($pid);
        if( empty($plugin->config) ) {
            throw new NotFoundHttpException('插件['.$pid.']没有配置设定。');
        }
        $settings = json_decode($plugin->settings, true);
        $configs = json_decode($plugin->config, true);
        foreach($configs as $key=>$config) {
            $config['value'] = $settings[$config['key']];
            $models[$key] = new Setting($config);
        }
        if( Model::loadMultiple($models, Yii::$app->getRequest()->post()) && Model::validateMultiple($models) ) {
            $plugin->settings = json_encode(ArrayHelper::map($models, 'key', 'value'));
            $plugin->save();
            self::createPluginsFile();
        }
        return $this->render('settings', [
            'plugin' => Util::convertModelToArray($plugin),
            'settings' => $models
        ]);
    }

    public static function createPluginsFile()
    {
        $plugins = Plugin::find()->orderBy(['id'=>SORT_ASC])->all();
        $result = [];
        foreach($plugins as $plugin) {
            $settings = [];
            if( !empty($plugin->settings) ) {
                $settings = json_decode($plugin->settings, true);
            }
			$handlerClass = 'app\plugins\\'.$plugin->pid.'\\'.$plugin->pid;
            if( !empty($plugin->events) ) {
                $events = json_decode($plugin->events, true);
                foreach($events as $type=>$event) {
                    $result['events'][$type][] = [ [$handlerClass, $event], $settings];
                }
            } else {
                $result['plugins'][$plugin->pid] = ['class'=>$handlerClass]+$settings;
            }
        }
        self::createFile($result);
        return;
    }

    protected static function createFile($settings)
    {
        $config = '<?php'."\n";
        $config = $config. 'return ';
        $config = $config. Util::convertArrayToString($settings, '  ').";\n";

        file_put_contents(Yii::getAlias('@app/config/plugins.php'), $config);
    }


    public static function getInstalledPlugins()
    {
        self::$installed = Plugin::find()->orderBy(['id'=>SORT_ASC])->indexBy('pid')->asArray()->all();
        return;
    }

    public static function getInstallablePlugin($pid)
    {
        $pluginFile = Yii::getAlias('@app/plugins/' . $pid . '/' . $pid . '.php');
        $handlerClass = 'app\plugins\\' . $pid . '\\' . $pid;
        if (!file_exists($pluginFile) || !is_callable([$handlerClass, 'info']) ) {
            return null;
        }
        if (isset(self::$installed[$pid]) || isset(self::$installable[$pid])) {
            return null;
        }
        $plugin = [
            'pid' => $pid,
            'handlerClass' => $handlerClass,
            'installed' => false,
        ];
        // else may be plugin make as inactive
        if (is_callable([$handlerClass, 'events'])) {
            $plugin['events'] = $handlerClass::events();
        } else {
            $plugin['events'] = [];
        }
        // add info to pool
        $plugin += $handlerClass::info();
        return $plugin;
    }

    public static function getInstallablePlugins()
    {
        $pluginsDir = Yii::getAlias('@app/plugins');
        if (!file_exists($pluginsDir)) {
            return [];
        }
        $plugins = array_diff(scandir($pluginsDir), ['.', '..']);
        foreach ($plugins as $pid) {
            $plugin = self::getInstallablePlugin($pid);
            if( $plugin ) {
                self::$installable[$pid] = $plugin;
            }
        }
    }
    protected function findModel($pid)
    {
        if (($model = Plugin::findOne(['pid'=>$pid])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('未找到['.$pid.']插件');
        }
    }

}
