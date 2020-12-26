<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\assets;

use yii\web\AssetBundle;

class LightboxAsset extends AssetBundle
{
//    public $basePath = '@webroot';
    public $baseUrl = '@web/static/assets/lightbox2/dist';
    public $css = [
        'css/lightbox.min.css',
    ];
    public $js = [
        'js/lightbox.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
