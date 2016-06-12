<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use app\lib\Util;

class BuyInviteCodeForm extends \yii\base\Model
{
    public $password;
    public $amount;

    public function rules()
    {
        return [
            ['amount', 'trim'],
            [['amount', 'password'], 'required'],
            ['password', 'string', 'length' => [6, 16]],
            ['amount', 'integer'],
            ['amount', 'validateCost'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'amount' => '购买数量',
            'password' => '登录密码',
        ];
    }

    public function validateCost($attribute, $params)
    {
        if ( Yii::$app->getUser()->getIdentity()->score - 50*intval($this->$attribute) < 0 ) {
            $this->addError($attribute, '您的积分不够');
        }
    }

    public function validatePassword($attribute, $params)
    {
        if ( !Yii::$app->getUser()->getIdentity()->validatePassword($this->$attribute) ) {
            $this->addError($attribute, '密码输入错误');
        }
    }

    public function apply()
    {
        $user = Yii::$app->getUser()->getIdentity();
        for($i=0; $i<intval($this->amount);$i++) {
            (new Token([
                'user_id' => $user->id,
                'type' => Token::TYPE_INVITE_CODE,
                'token' => Util::shorturl($user->id . '-' . $i . '-' . time() . '-' . Util::generateRandomString(32)),
                'expires' => 0,
                'ext' => json_encode(['source'=>'购买']),
            ]))->save(false);
        }
        $cost = User::getCost('buyInviteCode') * intval($this->amount);
        $user->updateScore($cost);
        (new History([
            'user_id' => $user->id,
            'type' => History::TYPE_POINT,
            'action' => History::ACTION_INVITE_CODE,
            'action_time' => time(),
            'target' => 0,
            'ext' => json_encode(['amount'=>$this->amount, 'score'=>$user->score, 'cost'=>$cost]),
        ]))->save(false);
    }

}
