<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\components;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\base\Event;

class Editor
{
    protected $_parser = null;

    public function registerAsset($view)
    {
    }

    public function registerUploadAsset($view)
    {
        $view->registerAssetBundle('app\assets\JqueryUploadFileAsset');
        $view->registerJs('$("#fileuploader").uploadFile({
            url:"'.Url::to(['service/upload']).'",
            fileName:"UploadForm[files]",
            returnType:"json",
            maxFileCount:4,
            maxFileSize:2*1024*1024,
            onSuccess:function(files,data,xhr,pd) {
                pd.filename.append("  <a id=\""+data+"\" class=\"insert-image\" href=\"javascript:void(0);\">'.Yii::t('app', 'Insert it').'</a>");
            }
        });');
    }

    public function registerTagItAsset($view)
    {
        $view->registerAssetBundle('app\assets\TagItAsset');
        $view->registerJs('$("#tags").tagit({
                caseSensitive:false,
                tagLimit:4,
                singleField:true,
                preprocessTag:function(val) {
                  if (!val) { return ""; }
                  if ( val.length>20 ) {
                    return val.substring(0, 20);
                  }
                  return val;
                }
            });');
    }

    public function parse($text)
    {
        $text = $this->parseEditor($text, !(intval(ArrayHelper::getValue(Yii::$app->params, 'settings.autolink', 0))===0));
        $hook = new SfHook(SfHook::EVENT_AFTER_PARSE);
//        $hook->bindEvents(SfHook::EVENT_AFTER_PARSE);
        $event = new SfEvent(['output'=>$text]);
        $hook->trigger(SfHook::EVENT_AFTER_PARSE, $event);
        return $event->output;
    }

    public function parseEditor($text)
    {
        return $text;
    }
}
