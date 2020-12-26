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
use app\models\User;
use app\components\SfHook;
use app\components\SfEvent;

/**
 * Signup form
 */
class SignupForm extends Model
{
    const ACTION_SIGNUP = 'signup';
    const ACTION_AUTH_SIGNUP = 'auth-signup';

    public $username;
    public $name;
    public $email;
    public $password;
    public $password_repeat;
    public $invite_code;
    public $action;
    public $captcha;
    private $_inviteCode = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['username', 'email', 'name', 'invite_code'], 'trim'],
            [['username', 'email', 'name', 'password', 'password_repeat'], 'required'],
            [['username', 'email'], 'filter', 'filter' => 'strtolower'],
            ['username', 'match', 'pattern' => User::USERNAME_PATTERN, 'message' => Yii::t('app', 'Your username can only contain letters, numbers and \'_\'.')],
            ['username', 'string', 'length' => [4, 16]],
//            ['username', 'validateMbString'],
            ['username', 'usernameFilter'],
            ['name', 'string', 'length' => [4, 40]],
            ['name', 'nameFilter'],
            ['email', 'email'],
            ['password', 'string', 'length' => [6, 16]],
            ['password_repeat', 'compare', 'skipOnEmpty'=>false, 'compareAttribute'=>'password', 'message' => Yii::t('app', 'Password confirmation doesn\'t match the password.')],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => Yii::t('app', '{attribute} is already in use.', ['attribute' => Yii::t('app', 'Username')])],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => Yii::t('app', '{attribute} is already in use.', ['attribute' => Yii::t('app', 'Email')])],
            ['invite_code', 'validateInviteCode'],
        ];

        if(intval(Yii::$app->params['settings']['close_register']) === 2) {
            $rules[] = ['invite_code', 'required'];
        }

        $captcha = ArrayHelper::getValue(Yii::$app->params, 'settings.captcha', '');
        if(!empty($captcha) && ($plugin=ArrayHelper::getValue(Yii::$app->params, 'plugins.' . $captcha, []))) {
           $rule = $plugin['class']::captchaValidate('signup', $plugin);
           if(!empty($rule)) {
             $rules[] = $rule;
           }
        }

        return $rules;
    }

    public function validateMbString($attribute, $params)
    {
        $len = strlen(preg_replace("/[\x{4e00}-\x{9fa5}]/u", '**', $this->$attribute));
        if ($len<6 || $len>16) {
            $this->addError($attribute, Yii::t('app', 'Username should contain 6-16 characters.'));
        }
    }

    public function usernameFilter($attribute, $params)
    {
        if( empty(Yii::$app->params['settings']['username_filter']) ) {
            return;
        }
        $filters = explode(',', Yii::$app->params['settings']['username_filter']);
        foreach($filters as $filter) {
            $pattern = str_replace('*', '.*', $filter);
            $result = preg_match('/^' . $pattern . '$/is', $this->$attribute);
            if ( !empty($result) ) {
                $this->addError($attribute, Yii::t('app', '{attribute} cannot contain "{value}".', ['attribute'=>Yii::t('app', 'Username'), 'value'=>str_replace('*', '', $filter)]));
                return;
            }
        }
    }

    public function nameFilter($attribute, $params)
    {
        if( empty(Yii::$app->params['settings']['name_filter']) ) {
            return;
        }
        $filters = explode(',', Yii::$app->params['settings']['name_filter']);
        foreach($filters as $filter) {
            $pattern = str_replace('*', '.*', $filter);
            $result = preg_match('/^' . $pattern . '$/is', $this->$attribute);
            if ( !empty($result) ) {
                $this->addError($attribute, Yii::t('app', '{attribute} cannot contain "{value}".', ['attribute'=>Yii::t('app', 'Name'), 'value'=>str_replace('*', '', $filter)]));
                return;
            }
        }
    }

    public function validateInviteCode($attribute, $params)
    {
        $this->_inviteCode = Token::find()
                    ->where(['token'=>$this->$attribute, 'type'=>Token::TYPE_INVITE_CODE])
                    ->one();
        if (!$this->_inviteCode) {
            $this->addError($attribute, Yii::t('app', '{attribute} is invalid.', ['attribute' => Yii::t('app', 'Invite code')]));
        } else if ($this->_inviteCode->status != Token::STATUS_VALID) {
            $this->addError($attribute, Yii::t('app', 'The invite code was used.'));
        } else if ($this->_inviteCode->expires > 0 && $this->_inviteCode->expires < time()) {
            $this->addError($attribute, Yii::t('app', 'The invite code has expired.'));
        }
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'password_repeat' => Yii::t('app', 'Confirm password'),
            'invite_code' => Yii::t('app', 'Invite code'),
            'captcha' => Yii::t('app', 'Enter code'),
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
            $user->name = $this->name;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->score = User::getCost('reg');
            $user->avatar = 'avatar/0_{size}.png';
            if ( $this->action != self::ACTION_AUTH_SIGNUP ) {
                if (intval(Yii::$app->params['settings']['email_verify']) === 1) {
                    $user->status = User::STATUS_INACTIVE;
                } else if (intval(Yii::$app->params['settings']['admin_verify']) === 1) {
                    $user->status = User::STATUS_ADMIN_VERIFY;
                } else {
                    $user->status = User::STATUS_ACTIVE;
                }
            } else {
                $user->status = User::STATUS_ACTIVE;
            }
            if ($user->save()) {
                if( $this->_inviteCode ) {
                    $this->_inviteCode->status = Token::STATUS_USED;
                    $this->_inviteCode->ext = json_encode(['id'=>$user->id, 'username'=>$user->username]);
                    $this->_inviteCode->save();
                }
                if ( $this->action != self::ACTION_AUTH_SIGNUP && intval(Yii::$app->params['settings']['email_verify']) === 1) {
                    Token::sendActivateMail($user);
                }
                return $user;
            }
        }

        return null;
    }
}
