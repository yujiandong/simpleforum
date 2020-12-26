<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models\admin;

use Yii;
use yii\base\Model;

class TestEmailForm extends Model
{
    public $email;
    public $content;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'content'], 'trim'],
            [['email', 'content'], 'required'],
            ['email', 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app/admin', 'Email To'),
            'content' => Yii::t('app/admin', 'Test Content'),
        ];
    }

    public function sendEmail()
    {
		$settings = Yii::$app->params['settings'];
	        $rtnCd = Yii::$app->getMailer()->compose()
	            ->setFrom([$settings['mailer_username'] => $settings['site_name']])
	            ->setTo($this->email)
	            ->setSubject($settings['site_name']. ' '. Yii::t('app/admin', 'Test Email Sending'))
				->setTextBody(\yii\helpers\Html::encode($this->content))
	            ->send();

        return $rtnCd;
    }

}
