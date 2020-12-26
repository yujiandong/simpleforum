<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\plugins\ReCaptcha;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\components\SfHook;
use app\components\PluginInterface;
use app\plugins\ReCaptcha\ReCaptchaWidget;
use app\models\Setting;

class ReCaptcha implements PluginInterface
{
    public static function info()
    {
        return [
            'id' => 'ReCaptcha',
            'name' => 'Google reCAPTCHA V3',
            'description' => 'Google reCAPTCHA V3',
            'author' => 'SimpleForum',
            'url' => 'http://simpleforum.org',
            'version' => '1.0',
            'config' => [
                [
                    'label'=>'siteKey',
                    'key'=>'siteKey',
                    'type'=>'text',
                    'value_type'=>'text',
                    'value'=>'',
                    'description'=>'',
                ],
                [
                    'label'=>'secretKey',
                    'key'=>'secretKey',
                    'type'=>'text',
                    'value_type'=>'text',
                    'value'=>'',
                    'description'=>'',
                ],
                [
                    'label'=>'threshold',
                    'key'=>'threshold',
                    'type'=>'text',
                    'value_type'=>'text',
                    'value'=>'0.5',
                    'description'=>'',
                ],
                [
                    'label'=>'checktarget',
                    'key'=>'target',
                    'type'=>'checkboxList',
                    'value_type'=>'text',
                    'value'=>['signup','signin','newtopic','newcomment', 'sms'],
                    'description'=>'',
                    'option'=>['signup'=>'Sign up','signin'=>'Sign in','newtopic'=>'Add Topic','newcomment'=>'Add Comment','sms'=>'Send Message']
                ],
            ],
        ];
    }

    public static function install()
    {
        if ( ($setting = Setting::findOne(['key'=>'captcha'])) ) {
            $option = json_decode($setting->option, true);
            $option['ReCaptcha']='Google reCAPTCHA V3';
            $setting->option = json_encode($option);
            $setting->save();
        }
        return true;
    }

    public static function uninstall()
    {
        if ( ($setting = Setting::findOne(['key'=>'captcha'])) ) {
            $option = json_decode($setting->option, true);
            unset($option['ReCaptcha']);
            $setting->option = json_encode($option);
            $setting->save();
        }
        return true;
    }

    public static function captchaValidate($checktarget, $config)
    {
        if (!in_array($checktarget, $config['target'])) {
          return false;
        }
        return ['captcha', ReCaptchaValidator::className(), 'secret'=>$config['secretKey']];
    }

    public static function captchaWidget($checktarget, $form, $model, $action, $config)
    {
        if (!in_array($checktarget, $config['target'])) {
          return false;
        }
        if(!empty($action)) {
            $action = sha1($action);
       }

echo $form->field($model, 'captcha')->widget(
    ReCaptchaWidget::className(),
    [
        'siteKey'=> $config['siteKey'],
        'action' => $action,
    ]
)->label(false);
        return true;
    }

}
