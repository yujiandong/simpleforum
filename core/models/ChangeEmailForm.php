<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
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
            ['password', 'string', 'length' => [6, 16]],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => '申请绑定邮箱已被注册使用'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => '新邮箱',
            'password' => '登录密码',
        ];
    }

	public function apply()
	{
		$user = Yii::$app->getUser()->getIdentity();
		if ( !$user->validatePassword($this->password) ) {
			return ['chgEmailNG', '[登录密码]错误'];
		} else if ( $user->email === $this->email ) {
			return ['chgEmailNG', '[新邮箱]和[当前邮箱]不能相同'];
		} else if ( !$this->validate() ) {
			return ['chgEmailNG', implode('<br />', $this->getFirstErrors())];
		} else if ( !self::sendEmail() ) {
			return ['chgEmailNG', '邮件发送出错，请重新申请或联系站长('.Yii::$app->params['settings']['admin_email'].')'];
		} else {
			return ['chgEmailOK', '验证网址已发送到您的新邮箱，请进邮箱点击确认。'];
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
		        $rtnCd = Yii::$app->getMailer()->compose(['html' => 'emailChangeToken-text'], ['token' => $token])
	                ->setFrom([$settings['mailer_username'] => $settings['site_name']])
	                ->setTo($this->email)
	                ->setSubject($settings['site_name']. '修改邮箱确认')
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
