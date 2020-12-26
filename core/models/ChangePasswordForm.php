<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
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
            ['password', 'compare', 'compareAttribute'=>'old_password', 'operator' => '!=', 'message' => Yii::t('app', '{attribute} must not be equal to {compareAttribute}.', ['attribute' => Yii::t('app', 'New password'), 'compareAttribute' => Yii::t('app', 'Current password')])],
            ['password_repeat', 'compare', 'skipOnEmpty'=>false, 'compareAttribute'=>'password', 'message' => Yii::t('app', 'Password confirmation doesn\'t match the password.')],
        ];
    }

    public function attributeLabels()
    {
        return [
            'old_password' => Yii::t('app', 'Current password'),
            'password' => Yii::t('app', 'New password'),
            'password_repeat' => Yii::t('app', 'Confirm password'),
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
                'ext' => '',
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
            $result = ['chgPwdNG', Yii::t('app', '{attribute} is invalid.', ['attribute' => Yii::t('app', 'Current password')])];
        } else if ( !$this->savePassword() ) {
            $result = ['chgPwdNG', Yii::t('app', 'Error Occurred. Please try again.')];
        } else {
            $result = ['chgPwdOK', Yii::t('app', '{attribute} has been changed successfully.', ['attribute' => Yii::t('app', 'Password')])];
        }
        return $result;
    }
}
