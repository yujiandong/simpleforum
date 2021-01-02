<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\plugins\MediaParser;

use Yii;
use app\components\SfHook;
use app\components\PluginInterface;

class MediaParser implements PluginInterface
{
    public static $parser = [
        'youku' => [
            'player.youku.com' => [
                '/([ \t\n\>]+|^)https?:\/\/player\.youku\.com\/player\.php\/.*?sid\/([a-zA-Z0-9\=]+)\/v\.swf/',
                '\1<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://player.youku.com/embed/\2" allowfullscreen></iframe></div>'
            ],
            'v.youku.com' => [
                '/([ \t\n\>]+|^)https?:\/\/v\.youku\.com\/v_show\/id_([a-zA-Z0-9\=]+)(\/|\.html?)?/',
                '\1<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://player.youku.com/embed/\2" allowfullscreen></iframe></div>'
            ],
        ],
        'tudou' => [
            'www.tudou.com' => [
                [
                    '/([ \t\n\>]+|^)https?:\/\/(www\.tudou\.com\/[a-z]\/[a-zA-Z0-9\=]+\/(\&amp;resourceId\=[0-9\_]+|\&amp;iid\=[0-9\_]+)*(\/v\.swf)?)/',
                    '/([ \t\n\>]+|^)https?:\/\/www\.tudou\.com\/(programs\/view|listplay|albumplay)\/([a-zA-Z0-9\=\_\-]+)\/([a-zA-Z0-9\=\_\-]+)(\/|\.html)?/',
                ],
                [
                    '\1<div class="embed-responsive embed-responsive-16by9"><embed class="embed-responsive-item" src="http://\2" quality="high" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash"></embed></div>',
                    '\1<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="http://www.tudou.com/programs/view/html5embed.action?type=1&code=\4&lcode=\3&resourceId=0_06_05_99" allowtransparency="true" allowfullscreen="true" allowfullscreenInteractive="true" scrolling="no" border="0" frameborder="0"></iframe></div>',
                ]
            ],
        ],
        'qq' => [
            'v.qq.com' => [
                [
                    '/([ \t\n\>]+|^)https?:\/\/v\.qq\.com\/[a-zA-Z0-9\/]+\.html\?vid=([a-zA-Z0-9]{8,})/',
                    '/([ \t\n\>]+|^)https?:\/\/v\.qq\.com\/[a-zA-Z0-9\/]+\/([a-zA-Z0-9]{8,})\.html/',
                ],
                [
                    '\1<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://v.qq.com/iframe/player.html?vid=\2&tiny=0&auto=0" allowfullscreen></iframe></div>',
                    '\1<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://v.qq.com/iframe/player.html?vid=\2&tiny=0&auto=0" allowfullscreen></iframe></div>',
                ]
            ],
            'film.qq.com' => [
                '/([ \t\n\>]+|^)https?:\/\/film\.qq\.com\/[a-zA-Z0-9\/]+\/([a-zA-Z0-9]{8,})\.html/',
                '\1<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://v.qq.com/iframe/player.html?vid=\2&tiny=0&auto=0" allowfullscreen></iframe></div>'
            ],
        ],
        'youtube' => [
            'youtu.be' => [
                '/([ \t\n\>]+|^)https?:\/\/youtu\.be\/([a-zA-Z0-9\=\_\-]+)/',
                '\1<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/\2" allowfullscreen></iframe></div>'
            ],
            'www.youtube.com' => [
                '/([ \t\n\>]+|^)https?:\/\/www\.youtube\.com\/watch\?v\=([a-zA-Z0-9\=\_\-]+)/',
                '\1<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/\2" allowfullscreen></iframe></div>'
            ],
        ],
    ];

    public static function info()
    {
        return [
            'id' => 'MediaParser',
            'name' => '媒体地址自动解析',
            'description' => '自动解析视频地址或音乐地址',
            'author' => 'SimpleForum',
            'url' => 'http://simpleforum.org',
            'version' => '1.0',
            'config' => [
                [
                    'label'=>'解析对象',
                    'key'=>'target',
                    'type'=>'checkboxList',
                    'value_type'=>'text',
                    'value'=>['youku','tudou','qq','youtube','miaopai'],
                    'description'=>'',
                    'option'=>['youku'=>'优酷','tudou'=>'土豆','qq'=>'qq视频','youtube'=>'Youtube','miaopai'=>'秒拍']
                ],
            ],
        ];
    }

    public static function install()
    {
        return true;
    }

    public static function uninstall()
    {
        return true;
    }

    public static function events()
    {
        return [
            SfHook::EVENT_AFTER_PARSE => 'parseMedia'
        ];
    }

    public static function parseMedia($event)
    {
        if ( !isset($event->output) || empty($event->output)) {
            return true;
        }
        $text = $event->output;
        foreach($event->data['target'] as $target) {
            if( !isset(static::$parser[$target]) ) {
                continue;
            }
            foreach(static::$parser[$target] as $key=>$parser) {
                if(strpos($text, $key)) {
                    $text = preg_replace($parser[0], $parser[1], $text);
                }
            }
        }
        $event->output = $text;
        return true;
    }

}
