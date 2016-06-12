<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use app\lib\Util;

class SendMsgForm extends \yii\base\Model
{
    public $username;
    public $msg;
    protected $_user;

    public function rules()
    {
        return [
            [['username', 'msg'], 'trim'],
            [['username', 'msg'], 'required'],
            ['username', 'string', 'max'=>16],
            ['msg', 'string', 'max'=>255],
            ['username', 'validateUsername'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'msg' => '消息',
        ];
    }

    public function validateUsername($attribute, $params)
    {
        $me = Yii::$app->getUser()->getIdentity();
      if( $me->username == $this->$attribute) {
            $this->addError($attribute, '不能给自己发消息');
            return;
        }
        $this->_user = User::findOne(['username'=>$this->$attribute]);
        if ( !$this->_user ) {
            $this->addError($attribute, '该会员不存在');
        }
    }

    public function apply()
    {
        $me = Yii::$app->getUser()->getIdentity();
        (new Notice([
            'target_id' => $this->_user->id,
            'source_id' => $me->id,
            'type' => Notice::TYPE_MSG,
            'msg' => $this->msg,
        ]))->save(false);
        $cost = User::getCost('sendMsg');
        $me->updateScore($cost);
        (new History([
            'user_id' => $me->id,
            'type' => History::TYPE_POINT,
            'action' => History::ACTION_MSG,
            'action_time' => time(),
            'target' => $this->_user->id,
            'ext' => json_encode(['score'=>$me->score, 'cost'=>$cost, 'target'=>$this->username, 'msg'=>$this->msg]),
        ]))->save(false);
        return true;
    }

}
