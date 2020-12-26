<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;
use app\lib\Util;

class ForgotPasswordForm extends Model
{
    public $email;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => Yii::t('app', 'That email address is not associated with a personal user account.')
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
        ];
    }

    public function apply()
    {
        if ( !self::findUser() ) {
            throw new InvalidParamException(Yii::t('app', 'That email address is not associated with a personal user account.'));
        } else if ( !self::sendEmail() ) {
            throw new InvalidParamException(Yii::t('app', 'An error occured when sending email. Please try later or contact the administrator.'));
        } else {
            return true;
        }
    }

    public function findUser()
    {
        $this->_user = User::findByEmail($this->email);
        if ( !$this->_user ) {
            return false;
        }
        return true;
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        $user = $this->_user;

        $settings = Yii::$app->params['settings'];

        $token = Token::findByType(Token::TYPE_PWD, $user->id);
        $rtnCd = false;
        if ( $token ) {
            try {
                $rtnCd = Yii::$app->getMailer()->compose(['html' => '@app/mail/' . Yii::$app->language . '/passwordResetToken-text'], ['token' => $token])
                    ->setFrom([$settings['mailer_username'] => $settings['site_name']])
                    ->setTo($this->email)
                    ->setSubject(Yii::t('app', '{name}: Reset password', ['name' => $settings['site_name']]))
                    ->send();
            } catch(\Exception $e) {
                return false;
            }

            (new History([
                'user_id' => $user->id,
                'action' => History::ACTION_RESET_PWD,
                'ext' => '',
            ]))->save(false);
        }

        return $rtnCd;
    }
}
