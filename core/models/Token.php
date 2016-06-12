<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use app\lib\Util;

class Token extends ActiveRecord
{
    const TYPE_REG = 0;
    const TYPE_PWD = 1;
    const TYPE_EMAIL = 2;
    const TYPE_INVITE_CODE = 3;
    const STATUS_VALID = 0;
    const STATUS_USED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%token}}';
    }

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

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
            ->select(['id', 'username', 'status', 'email']);
    }

    public static function generateToken($length=32)
    {
        return Util::generateRandomString($length);
    }

    public static function findByToken($token, $type=self::TYPE_PWD)
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('网址参数不正确');
        }

       $model = static::find()->where(['token'=>$token])->with(['user'])->one();

        if (!$model || $model->type != $type) {
            throw new InvalidParamException('网址参数不正确');
        } else if ( $model->status == self::STATUS_VALID && $model->expires > 0 && $model->expires < time() || !$model->user ) {
            throw new InvalidParamException('该网址已超过有效期，请重新申请');
        } else if ($model->status == self::STATUS_USED ) {
            throw new InvalidParamException('该网址被使用过，已失效');
        }
        return $model;
    }

    public static function sendActivateMail($user, $email='')
    {
        $settings = Yii::$app->params['settings'];
        $email = empty($email)? $user['email']:$email;

        $model = self::findByType(self::TYPE_REG, $user['id'], $email);

        if ( $model ) {
            try {
                return Yii::$app->getMailer()->compose('registerVerifyToken-text', ['token' => $model, 'username'=>$user['username']])
                    ->setFrom([$settings['mailer_username'] => $settings['site_name']])
                    ->setTo($email)
                    ->setSubject($settings['site_name']. '会员注册激活')
                    ->send();
            } catch(\Exception $e) {
                return false;
            }
        }
        return false;
    }

    public static function findByType($type, $user_id, $ext='')
    {
        $model = static::find()
            ->where(['user_id'=>$user_id, 'type'=>$type, 'status'=>self::STATUS_VALID])
            ->andWhere(['>', 'expires', time()])
            ->orderBy(['expires'=>SORT_DESC])
            ->limit(1)
            ->one();
        if (!$model) {
            $model = new static([
                'user_id' => $user_id,
                'type' => $type,
                'expires' => time()+1800,
                'token' => self::generateToken(),
                'ext' => $ext,
            ]);
            if ( !$model->save(false) ) {
                $model = null;
            }
        }
        return $model;
    }

}
