<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\plugins\SfCaptcha;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\components\SfHook;
use app\components\PluginInterface;
use app\plugins\ReCaptcha\ReCaptchaWidget;
use app\models\Setting;

class SfCaptcha implements PluginInterface
{
    public static function info()
    {
        return [
            'id' => 'SfCaptcha',
            'name' => 'Simple Captcha',
            'description' => 'Simple Captcha',
            'author' => 'SimpleForum',
            'url' => 'http://simpleforum.org',
            'version' => '1.0',
            'config' => [
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
            $option['SfCaptcha']='Simple Captcha';
            $setting->option = json_encode($option);
            $setting->save();
        }
        return true;
    }

    public static function uninstall()
    {
        if ( ($setting = Setting::findOne(['key'=>'captcha'])) ) {
            $option = json_decode($setting->option, true);
            unset($option['SfCaptcha']);
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
        return ['captcha', 'captcha'];
    }

    public static function captchaWidget($checktarget, $form, $model, $action, $config)
    {
        if (!in_array($checktarget, $config['target'])) {
          return false;
        }
        echo $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname());
        return true;
    }

}
