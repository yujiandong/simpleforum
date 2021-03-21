<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\caching\DbDependency;
use yii\helpers\ArrayHelper;

class NaviNode extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%navi_node}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['navi_id', 'node_id'], 'integer'],
			['sortid', 'default', 'value' => 50],
			['visible', 'default', 'value' => 0],
        ];
    }

	public function getNode()
    {
        return $this->hasOne(Node::className(), ['id' => 'node_id'])
				->select(['id', 'name', 'ename']);
    }

	public function getNavi()
    {
        return $this->hasOne(Tag::className(), ['id' => 'navi_id'])
				->select(['id', 'name', 'ename']);
    }

	public static function afterNodeDelete($nodeId)
	{
		static::deleteAll(['node_id'=>$nodeId]);
	}

	public static function afterNaviDelete($naviId)
	{
		static::deleteAll(['navi_id'=>$naviId]);
	}

}
