<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use app\components\Util;

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
            'amount' => Yii::t('app', 'Purchase amount'),
            'password' => Yii::t('app', 'Password'),
        ];
    }

    public function validateCost($attribute, $params)
    {
        if ( !Yii::$app->getUser()->getIdentity()->checkActionCost('buyInviteCode') ) {
            $this->addError($attribute, Yii::t('app', 'You don\'t have enough points.'));
        }
    }

    public function validatePassword($attribute, $params)
    {
        if ( !Yii::$app->getUser()->getIdentity()->validatePassword($this->$attribute) ) {
            $this->addError($attribute, Yii::t('app', '{attribute} is invalid.', ['attribute'=>Yii::t('app', 'Password')]));
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
                'ext' => json_encode(['source'=>Yii::t('app', 'Buy')]),
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
