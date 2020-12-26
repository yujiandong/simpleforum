<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;

/**
 * Email reset form
 */
class ChangeEmailForm extends \yii\base\Model
{
    const SCENARIO_VERIFY_EMAIL = 2;

    public $password;
    public $email;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_VERIFY_EMAIL] = ['email'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            ['email', 'trim'],
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'filter', 'filter' => 'strtolower'],
            ['password', 'string', 'length' => [6, 16]],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => Yii::t('app', '{attribute} is already in use.', ['attribute' => Yii::t('app', 'Email')])],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'New email'),
            'password' => Yii::t('app', 'Password'),
        ];
    }

	public function apply()
	{
		$user = Yii::$app->getUser()->getIdentity();
		if ( !$user->validatePassword($this->password) ) {
			return ['chgEmailNG', Yii::t('app', '{attribute} is invalid.', ['attribute'=>Yii::t('app', 'Password')])];
		} else if ( $user->email === $this->email ) {
			return ['chgEmailNG', Yii::t('app', '{attribute} must not be equal to {compareAttribute}.', ['attribute'=>Yii::t('app', 'New email'), 'compareAttribute'=>Yii::t('app', 'Current email')])];
		} else if ( !$this->validate() ) {
			return ['chgEmailNG', implode('<br />', $this->getFirstErrors())];
		} else if ( !self::sendEmail() ) {
			return ['chgEmailNG', Yii::t('app', 'An error occured when sending email. Please try later or contact the administrator.')];
		} else {
			return ['chgEmailOK', Yii::t('app', 'An email has been sent to your email address containing a verification link. Please click on the link to verify your email. If you do not receive the email within a few minutes, please check your spam folder.')];
		}
	}

    public function sendEmail()
	{
        $user = Yii::$app->getUser()->getIdentity();
		if( intval($user->status) === User::STATUS_INACTIVE ) {
			return Token::sendActivateMail($user, $this->email);
		} else {
			return self::sendChangeEmail($user->id);
		}
	}

    public function sendChangeEmail($user_id)
    {
		$token = Token::findByType(Token::TYPE_EMAIL, $user_id, $this->email);
		$rtnCd = false;
		if ( $token ) {
			$settings = Yii::$app->params['settings'];

			try {
		        $rtnCd = Yii::$app->getMailer()->compose(['html' => '@app/mail/' . Yii::$app->language . '/emailChangeToken-text'], ['token' => $token])
	                ->setFrom([$settings['mailer_username'] => $settings['site_name']])
	                ->setTo($this->email)
	                ->setSubject(Yii::t('app', '{name}: Email change verification', ['name' => $settings['site_name']]))
	                ->send();
			} catch(\Exception $e) {
				return false;
			}

			(new History([
				'user_id' => $user_id,
				'action' => History::ACTION_CHANGE_EMAIL,
				'ext' => $this->email,
			]))->save(false);
		}

        return $rtnCd;
    }

}
