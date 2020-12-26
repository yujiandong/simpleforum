<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2020 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */
 
namespace app\components;
use yii\base\BootstrapInterface;

class LanguageSelector implements BootstrapInterface
{
    public $supportedLanguages = ['en-US', 'zh-CN', 'ja'];

    public function bootstrap($app)
    {
    	$default = empty($app->language)?'en-US':$app->language;
        $request = $app->getRequest();
        $preferredLanguage = $request->getCookies()->getValue('language', $default);
        // or in case of database:
        // $preferredLanguage = $app->user->language;
/*
        if (empty($preferredLanguage)) {
            $preferredLanguage = $request->getPreferredLanguage($this->supportedLanguages);
        }
*/
        $app->language = $preferredLanguage;
    }
}
