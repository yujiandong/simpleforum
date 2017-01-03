<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;

class Ad extends \yii\db\ActiveRecord
{
	const LOCATIONS = '{"0":"右侧","1":"下方", "2":"主题内容前", "3":"主题内容后", "4":"回复列表前"}';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ad}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
              [['location', 'name', 'content','expires'], 'required'],
              [['location', 'node_id', 'sortid'], 'integer'],
			  [['location', 'node_id'], 'default', 'value' => 0],
			  ['sortid', 'default', 'value' => 50],
              ['expires', 'date', 'format' => 'php:U-m-d'],
              ['name', 'string', 'max' => 20],
              ['content', 'string', 'max' => 5000],
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
            'location' => '位置',
            'node_id' => '节点',
            'expires' => '截止时间',
            'name' => '识别名称',
            'content' => '广告内容',
        ];
    }
}
