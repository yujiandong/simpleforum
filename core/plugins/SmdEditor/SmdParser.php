<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\plugins\SmdEditor;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class SmdParser extends \Parsedown
{
    protected function inlineImage($Excerpt)
    {
        $Inline = parent::inlineImage($Excerpt);
        if( !$Inline )
        {
            return;
        }
        $src = ArrayHelper::remove($Inline['element']['attributes'], 'src', '');
        $alt = ArrayHelper::remove($Inline['element']['attributes'], 'alt', '');
        $Inline['element']['attributes'] += [
            'src' => Yii::getAlias('@web/static/css/img/load.gif'),
            'data-original' => Html::encode($src),
            'alt' => Html::encode($alt),
            'class' => 'lazy',
        ];
        return $Inline;
    }

    protected function inlineUrl($Excerpt)
    {
        if ($this->urlsLinked !== true or ! isset($Excerpt['text'][2]) or $Excerpt['text'][2] !== '/')
        {
            return;
        }

        if (preg_match('/\bhttps?:[\/]{2}[^\s<]+\b\/*/ui', $Excerpt['context'], $matches, PREG_OFFSET_CAPTURE))
        {
            $exceptUrls = ArrayHelper::getValue(Yii::$app->params, 'settings.autolink_filter', []);
            foreach($exceptUrls as $url) {
                if ( strpos($matches[0][0], $url) !== false ) {
                    return;
                }
            }
            $Inline = array(
                'extent' => strlen($matches[0][0]),
                'position' => Html::encode($matches[0][1]),
                'element' => array(
                    'name' => 'a',
                    'text' => $matches[0][0],
                    'attributes' => array(
                        'href' => Html::encode($matches[0][0]),
                    ),
                ),
            );

            return $Inline;
        }
    }

    protected function inlineLink($Excerpt)
    {
        $Inline = parent::inlineLink($Excerpt);
        if( !$Inline ) {
            return;
        }
        $Inline['element']['attributes']['href'] = Html::encode($Inline['element']['attributes']['href']);
        return $Inline;
    }
}
