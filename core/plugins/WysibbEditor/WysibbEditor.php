<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\plugins\WysibbEditor;

use Yii;
use yii\helpers\Html;
use app\components\Util;
use app\components\Editor;
use app\components\PluginInterface;
use app\models\Setting;

class WysibbEditor extends Editor implements PluginInterface
{

    public static function info()
    {
        return [
            'id' => 'WysibbEditor',
            'name' => 'Wysibb编辑器(BBcode)',
            'description' => 'Wysibb编辑器(BBcode)',
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
            $option['WysibbEditor']='Wysibb编辑器(BBcode)';
            $setting->option = json_encode($option);
            $setting->save();
    }
        return true;
    }

    public static function uninstall()
    {
        if ( ($setting = Setting::findOne(['key'=>'editor'])) ) {
            $option = json_decode($setting->option, true);
            unset($option['WysibbEditor']);
            $setting->option = json_encode($option);
            $setting->save();
        }
        return true;
    }

    public function registerAsset($view)
    {
        WysibbAsset::register($view);
        $lang = empty(Yii::$app->language)?'en-US':Yii::$app->language;
        $view->registerJs("$('#editor').wysibb({lang:'{$lang}', buttons: 'bold,italic,underline,fontcolor,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,img,link,|,code,quote'});");
    }

    public function parseEditor($text)
    {
        if ( empty($this->_parser) ) {
            $this->_parser = new \Golonka\BBCode\BBCodeParser();
        }
        $this->_parser->setParser('image', '/\[img\](.*?)\[\/img\]/s', '<img src="'.\Yii::getAlias('@web/static/css/img/load.gif').'" data-original="$1" class="lazy" data-lightbox="zoom">', '$1');
        $this->_parser->setParser('listitem', '/\[\*\](.*?)\[\/\*\]/', '<li>$1</li>', '$1');
        $this->_parser->setParser('listitem2', '/\[\*\](.*)/', '<li>$1</li>', '$1');
        return Util::autoLink($this->_parser->parse(Html::encode($text)));
    }

}
