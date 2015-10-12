<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
	public $captcha;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
		if(intval(Yii::$app->params['settings']['captcha_enabled']) === 1) {
			$rules[] = ['captcha', 'captcha'];
		}
		return $rules;
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            'rememberMe' => '记住我一周',
            'captcha' => '验证码',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码不正确。');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            if ( ($status = Yii::$app->getUser()->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 7 : 0)) ) {
				$userIP = sprintf("%u", ip2long(Yii::$app->getRequest()->getUserIP()));
				UserInfo::updateAll([
					'last_login_at'=>time(),
					'last_login_ip'=>$userIP,
				], ['user_id'=> $this->getUser()->id]);
				(new History([
					'user_id' => $this->getUser()->id,
					'action' => History::ACTION_LOGIN,
					'target' => $userIP,
				]))->save(false);
			}

			return $status;

        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
