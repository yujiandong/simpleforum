<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;

/**
 * Password reset form
 */
class ChangePasswordForm extends \yii\base\Model
{
    public $old_password;
    public $password;
    public $password_repeat;
	private $_user;

    public function rules()
    {
        return [
            [['old_password', 'password', 'password_repeat'], 'required'],
            ['password', 'string', 'length' => [6, 16]],
            ['password', 'compare', 'compareAttribute'=>'old_password', 'operator' => '!=', 'message' => '[新密码]和[当前密码]不能相同'],
            ['password_repeat', 'compare', 'skipOnEmpty'=>false, 'compareAttribute'=>'password', 'message' => '[新密码]和[再次输入新密码]不一致'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'old_password' => '当前密码',
            'password' => '新密码',
            'password_repeat' => '再次输入新密码',
        ];
    }

    public function savePassword()
    {
		$user = $this->_user;
        $user->setPassword($this->password);

		if ( ($rtnCd = $user->save()) ) {
			(new History([
				'user_id' => $user->id,
				'action' => History::ACTION_CHANGE_PWD,
				'action_time' => $user->updated_at,
			]))->save(false);
		}

        return $rtnCd;
    }

	public function apply()
	{
		$this->_user = Yii::$app->getUser()->getIdentity();

		if ( !$this->validate() ) {
            $result = ['chgPwdNG', implode('<br />', $this->getFirstErrors())];
		} else if ( !$this->_user->validatePassword($this->old_password) ) {
            $result = ['chgPwdNG', '[当前密码]错误'];
		} else if ( !$this->savePassword() ) {
            $result = ['chgPwdNG', '程序出错，请重试'];
		} else {
            $result = ['chgPwdOK', '密码修改成功'];
		}
		return $result;
	}
}
