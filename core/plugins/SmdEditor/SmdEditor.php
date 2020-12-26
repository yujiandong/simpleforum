<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\plugins\SmdEditor;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\Setting;
use app\components\Editor;
use app\components\PluginInterface;

class SmdEditor extends Editor implements PluginInterface
{
    public static function info()
    {
        return [
            'id' => 'SmdEditor',
            'name' => 'Simple Markdown Editor',
            'description' => 'Simple Markdown Editor',
            'author' => 'SimpleForum',
            'url' => 'http://simpleforum.org',
            'version' => '1.0',
            'config' => [],
        ];
    }

    public static function install()
    {
        if ( ($setting = Setting::findOne(['key'=>'editor'])) ) {
            $option = json_decode($setting->option, true);
            $option['SmdEditor']='Simple Markdown Editor';
            $setting->option = json_encode($option);
            $setting->save();
        }
        return true;
    }

    public static function uninstall()
    {
        if ( ($setting = Setting::findOne(['key'=>'editor'])) ) {
            $option = json_decode($setting->option, true);
            unset($option['SmdEditor']);
            $setting->option = json_encode($option);
            $setting->save();
    }
        return true;
    }

    public function registerAsset($view)
    {
        SmdAsset::register($view);
        $lang = empty(Yii::$app->language)?'en-US':Yii::$app->language;
        $view->registerJs("var editor = new SimpleMarkdown({target: '#editor', lan:'{$lang}'});");
    }

    public function parseEditor($text, $autoLink=false)
    {
        if ( empty($this->_parser) ) {
            $this->_parser = new SmdParser();
        }
        return $this->_parser->setUrlsLinked($autoLink)->setMarkupEscaped(true)->text($text);
    }

}
