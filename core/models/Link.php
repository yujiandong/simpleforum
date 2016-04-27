<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;

class Link extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%link}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['name', 'url'], 'required'],
			['sortid', 'default', 'value' => 99],
            ['sortid', 'integer', 'min'=>0, 'max' => 99],
            ['name', 'string', 'max' => 20],
            ['url', 'string', 'max' => 200],
			['url', 'url', 'defaultScheme' => 'http'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sortid' => '排序',
            'name' => '链接文字',
            'url' => '链接网址',
        ];
    }

	public static function getLinks()
	{
		$key = 'links';
		$cache = Yii::$app->getCache();
		$settings = Yii::$app->params['settings'];

		if ( intval($settings['cache_enabled']) === 0 || ($links = $cache->get($key)) === false ) {
		    $links = static::find()
				->orderBy(['sortid'=>SORT_ASC, 'id'=>SORT_ASC])
				->asArray()
		        ->all();
			if ( intval($settings['cache_enabled']) !== 0 ) {
				if ($links === null) {
					$links = [];
				}
				$cache->set($key, $links, intval($settings['cache_time'])*60);
			}
		}
		return $links;
	}

}
