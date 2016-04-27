<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\install_update\models;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Admin Signup form
 */
class AdminSignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'trim'],
            [['username', 'email', 'password', 'password_repeat'], 'required'],
//            ['username', 'string', 'length' => [4, 20]],
			['username', 'match', 'pattern' => User::USERNAME_PATTERN, 'message' => '请使用字母(a-z),数字(0-9)或中文'],
            ['username', 'validateMbString'],
            ['email', 'email'],
            ['password', 'string', 'length' => [6, 16]],
            ['password_repeat', 'compare', 'skipOnEmpty'=>false, 'compareAttribute'=>'password', 'message' => '两次密码输入不一致'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => '用户名已存在'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => '邮箱已存在'],
        ];
    }

	public function validateMbString($attribute, $params)
    {
		$len = strlen(preg_replace("/[\x{4e00}-\x{9fa5}]/u", '**', $this->$attribute));
		if ($len<4 || $len>16) {
            $this->addError($attribute, '用户名长度为4到16位，1个中文等于2位');
		}
    }

    public function attributeLabels()
    {
        return [
            'username' => '管理员用户名',
            'email' => '电子邮件',
            'password' => '密码',
            'password_repeat' => '确认密码',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
			$user->status = User::STATUS_ACTIVE;
			$user->role = User::ROLE_ADMIN;
			$user->avatar = 'avatar/0_{size}.png';
            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }
}
