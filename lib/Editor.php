<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\lib;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;

class Editor extends Component
{
	public $editor = 'wysibb';
	private $_parser = null;

	public function registerAsset($view)
	{
			if ($this->editor === 'wysibb') {
				$view->registerAssetBundle('app\assets\WysibbAsset');
				$view->registerJs("$('#editor').wysibb({lang:'cn', buttons: 'bold,italic,underline,fontcolor,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,img,link,|,code,quote'});");
			} else if ($this->editor === 'smd') {
				$view->registerAssetBundle('app\assets\SmdAsset');
				$view->registerJs("var editor = new SimpleMarkdown({target: '#editor', lan:'zh-CN'});");
			} else if ($this->editor === 'simditor') {
				$view->registerAssetBundle('app\assets\SimditorAsset');
				$view->registerJs("var editor = new Simditor({textarea: $('#editor')});");
			}
    }

	public function registerUploadAsset($view)
	{
		$view->registerAssetBundle('app\assets\JqueryUploadFileAsset');
		$view->registerJs('$("#fileuploader").uploadFile({
			url:"'.Url::to(['user/upload']).'",
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
		return self::parseVideo(self::parseEditor($text));
	}

	public function parseEditor($text)
	{
		if ($this->editor === 'wysibb') {
			if ( empty($this->_parser) ) {
				$this->_parser = new \Golonka\BBCode\BBCodeParser();
			}
			$this->_parser->setParser('image', '/\[img\](.*?)\[\/img\]/s', '<img src="'.\Yii::getAlias('@web/static/css/img/grey.gif').'" data-original="$1" class="lazy">');
			return $this->_parser->parse(Html::encode($text));
		} else if ($this->editor === 'smd') {
			if ( empty($this->_parser) ) {
				$this->_parser = new \app\lib\SimpleParsedown();
			}
//			return $this->_parser->setUrlsLinked(false)->setMarkupEscaped(true)->text($text);
			return $this->_parser->setMarkupEscaped(true)->text($text);
		} else if ($this->editor === 'simditor') {
			return \yii\helpers\HtmlPurifier::process($text);
		}
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

		return $text;
	}

}
