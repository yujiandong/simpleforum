<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use app\models\User;
use yii\base\Model;
use Yii;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $password_repeat;

    private $_token;


    /**
     * Creates a form model given a token.
     *
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        $this->_token = Token::findByToken($token);
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            ['password', 'string', 'length' => [6, 16]],
            ['password_repeat', 'compare', 'skipOnEmpty'=>false, 'compareAttribute'=>'password', 'message' => '[新密码]和[再次输入新密码]不一致'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => '新密码',
            'password_repeat' => '再次输入新密码',
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_token->user;
        $user->setPassword($this->password);

		$this->_token->status = Token::STATUS_USED;

        return $user->save() && $this->_token->save();
    }
}
