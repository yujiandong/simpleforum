<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
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
                pd.filename.append("  <a id=\""+data+"\" class=\"insert-image\" href=\"javascript:void(0);\">插入图片</a>");
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
//        return $this->parseVideo($this->parseEditor($text));
        $text = $this->parseEditor($text, !(intval(ArrayHelper::getValue(Yii::$app->params, 'settings.autolink', 0))===0));
        $hook = new SfHook();
        $hook->bindEvents(SfHook::EVENT_AFTER_PARSE);
        $event = new SfEvent(['output'=>$text]);
        $hook->trigger(SfHook::EVENT_AFTER_PARSE, $event);
        return $event->output;
    }

    public function parseEditor($text)
    {
        return $text;
    }

    public function parseVideo($text)
    {
        // youku
        if(strpos($text, 'player.youku.com')){
            $text = preg_replace('/http:\/\/player\.youku\.com\/player\.php\/.*?sid\/([a-zA-Z0-9\=]+)\/v\.swf/', '<iframe class="video-link" src="http://player.youku.com/embed/\1" frameborder=0 allowfullscreen></iframe>', $text);
        }
        if(strpos($text, 'v.youku.com')){
            $text = preg_replace('/http:\/\/v\.youku\.com\/v_show\/id_([a-zA-Z0-9\=]+)(\/|\.html?)?/', '<iframe class="video-link" src="http://player.youku.com/embed/\1" frameborder=0 allowfullscreen></iframe>', $text);
        }
        // tudou
        if(strpos($text, 'www.tudou.com')){
                $text = preg_replace('/(http:\/\/www\.tudou\.com\/[a-z]\/([a-zA-Z0-9\=]+)\/(\&amp;resourceId\=[0-9\_]+|\&amp;iid\=[0-9\_]+)*(\/v\.swf)?)/', '<embed class="video-link" src="\\1" quality="high" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash"></embed>', $text);
                $text = preg_replace('/http:\/\/www\.tudou\.com\/(programs\/view|listplay)\/([a-zA-Z0-9\=\_\-]+)\/([a-zA-Z0-9\=\_\-]+)(\.html)?/', '<iframe class="video-link" src="http://www.tudou.com/programs/view/html5embed.action?type=1&code=\3&lcode=\2&resourceId=0_06_05_99" allowtransparency="true" allowfullscreen="true" allowfullscreenInteractive="true" scrolling="no" border="0" frameborder="0"></iframe>', $text);
                $text = preg_replace('/http:\/\/www\.tudou\.com\/albumplay\/([a-zA-Z0-9\=\_\-]+)\/([a-zA-Z0-9\=\_\-]+)(\.html)?/', '<iframe class="video-link" src="http://www.tudou.com/programs/view/html5embed.action?type=2&code=\2&lcode=\1&resourceId=0_06_05_99" allowtransparency="true" allowfullscreen="true" allowfullscreenInteractive="true" scrolling="no" border="0" frameborder="0"></iframe>', $text);
        }
        // qq
        if(strpos($text, 'v.qq.com')){
            if(strpos($text, 'vid=')){
                $text = preg_replace('/http:\/\/v\.qq\.com\/(.+)vid=([a-zA-Z0-9]{8,})/', '<embed class="video-link" src="http://static.video.qq.com/TPout.swf?vid=\2&auto=0" allowFullScreen="true" quality="high" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>', $text);
            }else{
                $text = preg_replace('/http:\/\/v\.qq\.com\/(.+)\/([a-zA-Z0-9]{8,})\.(html?)/', '<embed  class="video-link" src="http://static.video.qq.com/TPout.swf?vid=\2&auto=0" allowFullScreen="true" quality="high" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>', $text);
            }
        }
        // youtube
        if(strpos($text, 'youtu.be')){
            $text = preg_replace('/https?:\/\/youtu\.be\/([a-zA-Z0-9\-]+)/', '<iframe class="video-link" src="https://www.youtube.com/embed/\1" frameborder=0 allowfullscreen></iframe>', $text);
        }
        if(strpos($text, 'www.youtube.com')){
            $text = preg_replace('/https?:\/\/www\.youtube\.com\/watch\?v\=([a-zA-Z0-9\-]+)/', '<iframe class="video-link" src="https://www.youtube.com/embed/\1" frameborder=0 allowfullscreen></iframe>', $text);
        }

        return $text;
    }

}
