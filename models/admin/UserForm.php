<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models\admin;

use Yii;
use yii\base\Model;
use app\models\User;

class UserForm extends Model
{
    const SCENARIO_EDIT = 1;
    const SCENARIO_RESET_PWD = 2;

    public $id;
    public $username;
    public $status;
    public $email;
    public $password;
	private $_user;

	public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_EDIT] = ['email', 'status', '!username', '!id'];
        $scenarios[self::SCENARIO_RESET_PWD] = ['password'];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'integer', 'max'=>User::STATUS_ACTIVE, 'min'=>User::STATUS_BANNED],
            [['email'], 'trim'],
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['password', 'string', 'length' => [6, 16]],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'filter' => 'id != '. $this->_user->id, 'message' => '邮箱已存在'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'status' => '权限',
            'email' => '电子邮件',
            'password' => '密码',
        ];
    }

    public function find($id)
	{
			$this->_user = User::findOne($id);
			if($this->_user !== null) {
				$this->attributes = $this->_user->attributes;
				$this->username = $this->_user->username;
				$this->id = $this->_user->id;
			}
			return ($this->_user !== null);
	}

    public function edit()
    {
        if ($this->validate()) {
        	$user = $this->_user;
            $user->status = $this->status;
            $user->email = $this->email;
            return $this->_user->save(false);
        }

        return false;
    }
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        return $user->save();
    }
}
