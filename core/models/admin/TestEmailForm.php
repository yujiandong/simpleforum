<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
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
            'email' => '测试邮箱',
            'content' => '测试邮件内容',
        ];
    }

    public function sendEmail()
    {
		$settings = Yii::$app->params['settings'];
		try {
	        $rtnCd = Yii::$app->getMailer()->compose()
	            ->setFrom([$settings['mailer_username'] => $settings['site_name']])
	            ->setTo($this->email)
	            ->setSubject($settings['site_name']. '测试邮件')
				->setTextBody(\yii\helpers\Html::encode($this->content))
	            ->send();
		} catch(\Exception $e) {
			$rtnCd = false;
		}

        return $rtnCd;
    }

}
