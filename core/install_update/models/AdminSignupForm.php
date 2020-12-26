<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\install_update\models;

use Yii;
use yii\base\Model;
use app\models\User;
use app\models\History;

/**
 * Admin Signup form
 */
class AdminSignupForm extends Model
{
    public $username;
    public $name;
    public $email;
    public $password;
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'name', 'email'], 'trim'],
            [['username', 'name', 'email', 'password', 'password_repeat'], 'required'],
            [['username', 'email'], 'filter', 'filter' => 'strtolower'],
            ['username', 'string', 'length' => [4, 16]],
            ['username', 'match', 'pattern' => User::USERNAME_PATTERN, 'message' => Yii::t('app', 'Your username can only contain letters, numbers and \'_\'.')],
//            ['username', 'validateMbString'],
            ['name', 'string', 'length' => [4, 40]],
            ['email', 'email'],
            ['password', 'string', 'length' => [6, 16]],
            ['password_repeat', 'compare', 'skipOnEmpty'=>false, 'compareAttribute'=>'password', 'message' => Yii::t('app', 'Password confirmation doesn\'t match the password.')],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => Yii::t('app', '{attribute} is already in use.', ['attribute' => Yii::t('app', 'Username')])],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => Yii::t('app', '{attribute} is already in use.', ['attribute' => Yii::t('app', 'Email')])],
        ];
    }

    public function validateMbString($attribute, $params)
    {
        $len = strlen(preg_replace("/[\x{4e00}-\x{9fa5}]/u", '**', $this->$attribute));
        if ($len<6 || $len>16) {
            $this->addError($attribute, Yii::t('app', 'Username should contain 6-16 characters.'));
        }
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app/admin', 'Admin username'),
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'password_repeat' => Yii::t('app', 'Confirm password'),
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
            $user->name = $this->name;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->status = User::STATUS_ACTIVE;
            $user->role = User::ROLE_ADMIN;
            $user->avatar = 'avatar/0_{size}.png';
            $user->score = User::getCost('reg');
            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }
}
