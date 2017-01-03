<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models\admin;

use Yii;

class Plugin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugin}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['pid', 'name'], 'required'],
            [['pid', 'name', 'author'], 'string', 'max' => 20],
            [['description', 'url'], 'string', 'max' => 255],
            ['version', 'string', 'max' => 10],
            ['url', 'string', 'max' => 200],
			['url', 'url', 'defaultScheme' => 'http'],
            [['config', 'settings', 'events'], 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => '插件ID',
            'name' => '插件名',
            'author' => '作者',
            'url' => '网址',
            'version' => '版本',
            'description' => '描述',
            'status' => '状态',
        ];
    }
}
