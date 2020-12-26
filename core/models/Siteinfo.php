<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;

class Siteinfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%siteinfo}}';
    }

	public static function updateCounterInfo($action)
	{
		$upd = [
			'addTopic' => ['topics'=>1],
			'deleteTopic' => ['topics'=>-1],
			'addComment' => ['comments'=>1],
			'deleteComment' => ['comments'=>-1],
			'addNode' => ['nodes'=>1],
			'deleteNode' => ['nodes'=>-1],
			'addUser' => ['users'=>1],
			'deleteUser' => ['users'=>-1],
		];

		if( !isset($upd[$action]) ) {
			return false;
		}
		return static::updateAllCounters($upd[$action], ['id'=>1]);
	}

	public static function updateCountersInfo($upd)
	{
		return static::updateAllCounters($upd, ['id'=>1]);
	}

	public static function getSiteInfo()
	{
		$key = 'site-info';
		$cache = Yii::$app->getCache();
		$settings = Yii::$app->params['settings'];

		if ( intval($settings['cache_enabled']) === 0 || ($siteinfo = $cache->get($key)) === false ) {
		    $siteinfo = static::find()->where(['id' => 1])
				->asArray()
		        ->one();
			if ( intval($settings['cache_enabled']) !== 0 ) {
				if ($siteinfo === null) {
					$siteinfo = [];
				}
				$cache->set($key, $siteinfo, intval($settings['cache_time'])*60);
			}
		}
		return $siteinfo;
	}

}
