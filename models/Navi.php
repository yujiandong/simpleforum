<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class Navi extends ActiveRecord
{
/*	const TYPES = [
		0 => '节点导航页',
		1 => '首页头部',
		2 => '首页尾部',
	];
*/
	const TYPES = '{"0":"所有节点页","1":"首页头部", "2":"首页尾部"}';
	const TYPE_ALLNODES = 0;
	const TYPE_TOP = 1;
	const TYPE_BOTTOM = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%navi}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
              [['name', 'ename'], 'trim'],
              [['name', 'ename', 'type'], 'required'],
              [['type', 'sortid'], 'integer'],
//			  ['type', 'default', 'value' => 0],
			  ['sortid', 'default', 'value' => 50],
			  [['name', 'ename'], 'string', 'max' => 20],
			  ['ename', 'match', 'pattern' => '/^[a-z0-9\-]*$/i'],
              [['name', 'type'], 'unique', 'targetAttribute' => ['name', 'type']],
              [['ename', 'type'], 'unique', 'targetAttribute' => ['ename', 'type']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '导航名',
            'ename' => '导航英文名',
            'type' => '类型',
            'sortid' => '排序',
        ];
    }

	public function getVisibleNodes()
    {
        return $this->hasMany(Node::className(), ['id' => 'node_id'])
			->viaTable(NaviNode::tableName(), ['navi_id' => 'id'], 
                        function($query) {
                          $query->onCondition([NaviNode::tableName().'.visible'=>1])->orderBy(['sortid'=>SORT_ASC, 'id'=>SORT_DESC]);
				});
    }

	public function getNodes()
    {
        return $this->hasMany(Node::className(), ['id' => 'node_id'])
			->viaTable(NaviNode::tableName(), ['navi_id' => 'id'], 
                        function($query) {
                          $query->orderBy(['sortid'=>SORT_ASC, 'id'=>SORT_DESC]);
				});
    }

	public function getVisibleNaviNodes()
    {
        return $this->hasMany(NaviNode::className(), ['navi_id' => 'id'])
						->select(['node_id', 'navi_id'])
						->where((['visible'=>1]))
                          ->orderBy(['sortid'=>SORT_ASC, 'id'=>SORT_ASC]);
    }

	public function getNaviNodes()
    {
        return $this->hasMany(NaviNode::className(), ['navi_id' => 'id'])
						->select(['node_id', 'navi_id'])
                          ->orderBy(['sortid'=>SORT_ASC, 'id'=>SORT_ASC]);
    }

	public static function getHeadNaviNodes()
	{
		$key = 'head-navis';
		$cache = Yii::$app->getCache();
		$settings = Yii::$app->params['settings'];

		if ( intval($settings['cache_enabled']) === 0 || ($models = $cache->get($key)) === false ) {
			$models = static::find()->where(['type'=>self::TYPE_TOP])->orderBy(['sortid'=>SORT_ASC, 'id'=>SORT_ASC])->asArray()->all();
			if ( intval($settings['cache_enabled']) !== 0 ) {
				if ($models === null) {
					$models = [];
				}
				$cache->set($key, $models, intval($settings['cache_time'])*60);
			}
		}
		return $models;
	}

	public static function getBottomNaviNodes()
	{
		return static::find()->with(['naviNodes.node'])->where(['type'=>self::TYPE_BOTTOM])->orderBy(['sortid'=>SORT_ASC, 'id'=>SORT_ASC])->asArray()->all();
	}

	public static function getAllNaviNodes()
	{
		return static::find()->with(['naviNodes.node'])->where(['type'=>self::TYPE_ALLNODES])->orderBy(['sortid'=>SORT_ASC, 'id'=>SORT_ASC])->asArray()->all();
	}

}
