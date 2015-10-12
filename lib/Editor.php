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
				$view->registerJs("new SimpleMarkdown({target: '#editor', lan:'zh-CN'});");
			} else if ($this->editor === 'simditor') {
				$view->registerAssetBundle('app\assets\SimditorAsset');
				$view->registerJs("var editor = new Simditor({textarea: $('#editor')});");
			}
    }

	public function parse($text)
	{
		if ($this->editor === 'wysibb') {
			if ( empty($this->_parser) ) {
				$this->_parser = new \Golonka\BBCode\BBCodeParser();
			}
			return $this->_parser->parse(Html::encode($text));
		} else if ($this->editor === 'smd') {
			if ( empty($this->_parser) ) {
				$this->_parser = new \parsedown\Parsedown();
			}
			return $this->_parser->text(Html::encode($text));
		} else if ($this->editor === 'simditor') {
			return \yii\helpers\HtmlPurifier::process($text);
		}
	}
}
