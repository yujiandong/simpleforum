<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers\admin;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use app\models\Setting;
use app\lib\Util;

class SettingController extends CommonController
{
    public function actionIndex()
    {
        $settings = Setting::find()->indexBy('id')->all();

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
			$this->createConfigFile(ArrayHelper::map($settings, 'key', 'value'));
        }

		$newSettings = ArrayHelper::map($settings, 'id', function($item) {
			return $item;
		}, 'block');

        return $this->render('update', ['settings' => $newSettings]);
    }

    public function actionAll()
    {
        return $this->render('all');
    }

	public function actionClearCache()
	{
		Yii::$app->getCache()->flush();
	}

	private function createConfigFile($settings)
	{
		$settings = self::getCacheInfo($settings);
		$settings['footer_links'] = self::getFootLinks($settings['footer_links']);

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
		if ( $model->load(Yii::$app->getRequest()->post()) && $model->validate() ) {
			$rtnCd = $model->sendEmail() ? 1:9;
		}
        return $this->render('testEmail', ['model' => $model, 'rtnCd'=>$rtnCd]);
    }
}
