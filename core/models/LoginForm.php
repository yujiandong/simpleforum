<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use app\components\SfHook;
use app\components\SfEvent;
/**
 * Login form
 */
class LoginForm extends Model
{
    const SCENARIO_BIND = 2;

    public $username;
    public $password;
    public $rememberMe = true;
    public $captcha;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_BIND] = ['username', 'password'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['username', 'password'], 'required'],
            ['username', 'filter', 'filter' => 'strtolower'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
        $captcha = ArrayHelper::getValue(Yii::$app->params, 'settings.captcha', '');
        if(!empty($captcha) && ($plugin=ArrayHelper::getValue(Yii::$app->params, 'plugins.' . $captcha, []))) {
           $rule = $plugin['class']::captchaValidate('signin', $plugin);
           if(!empty($rule)) {
             $rules[] = $rule;
           }
        }

        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'rememberMe' => Yii::t('app', 'Remember me for one week'),
            'captcha' => Yii::t('app', 'Enter code'),
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
                $this->addError($attribute, Yii::t('app', 'The username or password you entered is incorrect.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is signed in successfully
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
                    'ext' => '',
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
