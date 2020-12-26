<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
//use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Auth extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'source','source_id'], 'required'],
            [['source'], 'string', 'max'=>20],
            [['source_id'], 'string', 'max'=>100]
        ];
    }
/*
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
       ];
    }
*/
	public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
			->select(['id']);
    }

}
