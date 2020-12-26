<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
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
            ['pid', 'string', 'max' => 20],
            [['name', 'author'], 'string', 'max' => 40],
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
            'id' => 'Id',
            'pid' => Yii::t('app/admin', 'Plugin id'),
            'name' => Yii::t('app/admin', 'Plugin name'),
            'author' => Yii::t('app', 'Author'),
            'url' => Yii::t('app', 'Homepage'),
            'version' => Yii::t('app', 'Version'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
