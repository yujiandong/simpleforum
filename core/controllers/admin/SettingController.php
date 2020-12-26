<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers\admin;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use app\models\Setting;
use app\components\Util;

class SettingController extends CommonController
{
    public function actionIndex()
    {
        $settings = Setting::find()->where(['<>', 'block', 'auth'])->indexBy('id')->all();

        if (Model::loadMultiple($settings, Yii::$app->getRequest()->post()) && Model::validateMultiple($settings)) {
            foreach ($settings as $key=>$setting) {
                if ( $setting->key == 'timezone' && !self::checkTimeZone($setting->value) ) {
                    $setting->value = date_default_timezone_get();
                    continue;
                } else if ($setting->value_type == 'integer') {
                    $setting->value = intval($setting->value);
                }
                $setting->save(false);
            }
            $this->createConfigFile();
        }

        $newSettings = ArrayHelper::map($settings, 'id', function($item) {
            return $item;
        }, 'block');

        return $this->render('update', ['settings' => $newSettings, 'languages'=>self::getLanguages()]);
    }

    public function actionAuth()
    {
        $configs = [
            'type'=>['label'=>Yii::t('app/admin', 'Type'), 'key'=>'type', 'type'=>'select', 'value_type'=>'text', 'value'=>'', 'description'=>'', 'option'=>'{"":"", "qq":"QQ", "weibo":"微博", "weixin":"微信", "github":"Github"}'],
            'sortid'=>['label'=>Yii::t('app/admin', 'Sort ID'), 'key'=>'sortid', 'type'=>'text', 'value_type'=>'integer', 'value'=>'1', 'description'=>''],
            'show'=>['label'=>Yii::t('app/admin', 'Show'), 'key'=>'show', 'type'=>'select', 'value_type'=>'integer', 'value'=>'0', 'description'=>'', 'option'=>'["0('.Yii::t('app', 'Login page').')", "1('.Yii::t('app', 'Login page and right of all pages').')"]'],
            'clientId'=>['label'=>'clientId', 'key'=>'clientId', 'type'=>'text', 'value_type'=>'text', 'value'=>'', 'description'=>''],
            'clientSecret'=>['label'=>'clientSecret', 'key'=>'clientSecret', 'type'=>'text', 'value_type'=>'text', 'value'=>'', 'description'=>''],
            'title'=>['label'=>Yii::t('app/admin', 'Title'), 'key'=>'title', 'type'=>'text', 'value_type'=>'text', 'value'=>'', 'description'=>'']
        ];
        $titles = [
            'qq'=>'qq登录',
            'weibo'=>'微博登陆',
            'weixin'=>'微信登陆',
            'weixinmp'=>'微信公众号',
        ];
        $clientIds = [
            'qq'=>'appid',
            'weibo'=>'App Key',
        ];
        $clientSecrets = [
            'qq'=>'appkey',
            'weibo'=>'App Secret',
        ];

        $settings = Setting::find()->where(['block'=>'auth'])->indexBy('key')->all();
        $enableModel = $settings['auth_enabled'];
        $settingModel = $settings['auth_setting'];
        unset($settings['auth_setting']);

        if( ($datas = Yii::$app->getRequest()->post('Setting', [])) && count($datas)>0) {
            $enableModel->value = $datas[0][0]['value'];
            $enableModel->save();
            unset($datas[0]);

            $newSettings = [];
            foreach($datas as $data) {
                $set = ArrayHelper::map($data, 'key', 'value');
                if(empty($set['type']) || empty($set['clientId']) || empty($set['clientSecret'])) {
                    continue;
                } else {
                   foreach($configs as $key=>$config) {
                        if ($config['value_type'] == 'integer') {
                            $set[$key] = intval($set[$key]);
                        }
                   }
                   $newSettings[$set['type']] = $set;
                }
            }
            ArrayHelper::multisort($newSettings, ['sortid'], [SORT_ASC]);
            $settingModel->value = json_encode($newSettings);
            $settingModel->save();
            $this->createConfigFile();
        }

        $auths = json_decode($settingModel->value, true);
        if (!empty($auths)) {
            foreach($auths as $type=>$auth) {
                foreach($configs as $key=>$config) {
                    if( $config['key'] === 'clientId' && isset($clientIds[$type]) ) {
                        $config['label'] = $clientIds[$type];
                    }
                    if( $config['key'] === 'clientSecret' && isset($clientSecrets[$type]) ) {
                        $config['label'] = $clientSecrets[$type];
                    }
                    if( $config['key'] === 'title' && isset($titles[$type]) ) {
                        $config['value'] = $titles[$type];
                    }
                    if( isset($auth[$config['key']]) ) {
                        $config['value'] = $auth[$config['key']];
                    }
                    $settings[$type][$key] = new Setting($config);
                }
            }
        } else {
            foreach($configs as $key=>$config) {
                $settings[1][$key] = new Setting($config);
            }
        }


        return $this->render('auth', ['settings' => $settings]);
    }

    public static function getLanguages()
    {
        $languageDir = Yii::getAlias('@app/messages');
        $default = ['en-US'];
        if (!file_exists($languageDir)) {
            return $default;
        }
        return $default + array_diff(scandir($languageDir), ['.', '..']);
    }


    public function actionAll()
    {
        return $this->render('all');
    }

    public function actionClearCache()
    {
        Yii::$app->getCache()->flush();
        return $this->render('clearCache');
    }

    private function createConfigFile()
    {
        $settings = Setting::find()->asArray()->all();
        $settings = ArrayHelper::map($settings, 'key', 'value');

        $settings = self::getCacheInfo($settings);
        $settings['footer_links'] = self::getFootLinks($settings['footer_links']);
        $settings['autolink_filter'] = self::textAreaToArray($settings['autolink_filter']);
        $settings['groups'] = self::getFootLinks($settings['groups']);
        $settings['auth_setting'] = json_decode($settings['auth_setting'], true);

        $config = '<?php'."\n";
        $config = $config. 'return ['."\n";
        $config = $config. '  \'settings\' => ';
        $config = $config. Util::convertArrayToString($settings, '  ')."\n";
        $config = $config. '];'."\n";

        file_put_contents(Yii::getAlias('@app/config/params.php'), $config);
    }

    private function checkTimeZone($timeZone)
    {
        return in_array($timeZone, \DateTimeZone::listIdentifiers());
    }

    private function getFootLinks($links)
    {
        $result = [];
        if( !empty($links) ) {
            $links = explode("\r\n", $links);
            foreach($links as $link) {
                $link = trim($link);
                if( empty($link) ) {
                    continue;
                }
                $result[] = explode(' ', $link);
            }
        }
        return $result;
    }

    private function getCacheInfo($settings)
    {
        $cache_class = [
            'apc' => ['yii\caching\ApcCache', 0],
            'memcache' => ['yii\caching\MemCache', 1],
            'memcached' => ['yii\caching\MemCache', 1],
        ];
        if ($settings['cache_time'] === 0) {
            $settings['cache_enabled'] = 0;
        }
        if( $settings['cache_enabled'] === 1 && $settings['cache_time']>0 && !empty($settings['cache_type']) && array_key_exists($settings['cache_type'], $cache_class) ) {
            if ( $cache_class[$settings['cache_type']][1] === 1) {
                if ( !empty($settings['cache_servers']) && ($cache_servers = self::getCacheServerInfo($settings['cache_servers'])) ) {
                    $settings['cache_info'] = [
                        'class' => $cache_class[$settings['cache_type']][0],
                        'useMemcached' => $settings['cache_type']==='memcached'?true:false,
                        'servers' => $cache_servers,
                    ];
                }
            } else {
                $settings['cache_info'] = [
                    'class' => $cache_class[$settings['cache_type']][0],
                ];
            }
        }
        unset($settings['cache_type'], $settings['cache_servers']);
        return $settings;
    }

    private function getCacheServerInfo($serverInfo)
    {
        $serverKeys = ['host', 'port', 'weight'];
        $servers = explode("\r\n", $serverInfo);
        $result = [];
        foreach($servers as $key=>$server) {
            $server = trim($server);
            if ( empty($server) ) {
                continue;
            }
            $info = explode(' ', $server);
            foreach($info as $k=>$v) {
                $result[$key][$serverKeys[$k]] = $v;
            }
        }
        return $result;
    }

    public function actionTestEmail()
    {
        $model = new \app\models\admin\TestEmailForm();
        $rtnCd = 0;
        $msg = '';
        if ( $model->load(Yii::$app->getRequest()->post()) && $model->validate() ) {
		  try {
            $model->sendEmail();
            $rtnCd = 1;
          } catch(\Exception $e) {
            $rtnCd = 9;
            $msg = $e->getMessage();
          }
        }
        return $this->render('testEmail', ['model' => $model, 'rtnCd'=>$rtnCd, 'msg'=>$msg]);
    }

    private function textAreaToArray($links)
    {
        $result = [];
        if( !empty($links) ) {
            $links = explode("\r\n", $links);
            foreach($links as $link) {
                $link = trim($link);
                if( empty($link) ) {
                    continue;
                }
                $result[] = $link;
            }
        }
        return $result;
    }

}
