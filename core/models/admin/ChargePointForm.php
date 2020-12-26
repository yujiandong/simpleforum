<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models\admin;

use Yii;
use app\lib\Util;
use app\models\User;
use app\models\History;
use app\models\Notice;

class ChargePointForm extends \yii\base\Model
{
    public $username;
    public $msg;
    public $point;
    protected $_user;

    public function rules()
    {
        return [
            [['username', 'msg', 'point'], 'trim'],
            [['username', 'point'], 'required'],
            ['username', 'string', 'max'=>16],
            ['msg', 'string', 'max'=>255],
            ['point', 'integer'],
            ['username', 'validateUsername'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'point' => Yii::t('app', 'Charge Points'),
            'msg' => Yii::t('app', 'Message'),
        ];
    }

    public function validateUsername($attribute, $params)
    {
        $this->_user = User::findOne(['username'=>$this->$attribute]);
        if ( !$this->_user ) {
            $this->addError($attribute, Yii::t('app', '{attribute} doesn\'t exist.', ['attribute'=>Yii::t('app', 'Username')]));
        }
    }

    public function apply()
    {
        $me = Yii::$app->getUser()->getIdentity();
        $this->_user->updateScore($this->point);
        (new History([
            'user_id' => $this->_user->id,
            'type' => History::TYPE_POINT,
            'action' => History::ACTION_CHARGE_POINT,
            'action_time' => time(),
            'ext' => json_encode(['score'=>$this->_user->score, 'cost'=>$this->point, 'msg'=>$this->msg]),
        ]))->save(false);
        (new Notice([
            'target_id' => $this->_user->id,
            'source_id' => $me->id,
            'type' => Notice::TYPE_CHARGE_POINT,
            'msg' => $this->point . '; ' . Yii::t('app', 'Message'). '：' .$this->msg,
        ]))->save(false);
        return true;
    }

}
