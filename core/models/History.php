<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class History extends \yii\db\ActiveRecord
{
    const TYPE_POINT = 1;

    const ACTION_REG = 1;
    const ACTION_LOGIN = 2;
    const ACTION_EDIT_PROFILE = 3;
    const ACTION_CHANGE_PWD = 4;
    const ACTION_RESET_PWD = 5;
    const ACTION_CHANGE_EMAIL = 6;
    const ACTION_AVATAR = 7;
    const ACTION_BIND_ACCOUNT = 8;
    const ACTION_UNBIND_ACCOUNT = 9;
    const ACTION_MSG = 10;
    const ACTION_ADD_TOPIC = 20;
    const ACTION_EDIT_TOPIC = 21;
    const ACTION_ADD_COMMENT = 22;
    const ACTION_EDIT_COMMENT = 23;
    const ACTION_ORIGINAL_SCORE = 30;
    const ACTION_SIGNIN = 31;
    const ACTION_SIGNIN_10DAYS = 32;
    const ACTION_INVITE_CODE = 33;
    const ACTION_INVITE = 34;
    const ACTION_INVITED = 35;
    const ACTION_COMMENTED = 36;

    const ACTION_DELETE_TOPIC = 50;
    const ACTION_DELETE_COMMENT = 51;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['ext', 'default', ''],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['action_time'],
                ],
            ],
        ];
    }

}
