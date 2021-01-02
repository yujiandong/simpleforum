<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
//    public $basePath = '@webroot';
    public $baseUrl = '@web/static';
    public $css = [
        'assets/font-awesome-4.7.0/css/font-awesome.min.css',
        'css/default.css',
    ];
    public $js = [
        'js/jquery.lazyload.min.js',
        'assets/jquery-qrcode/jquery.qrcode.min.js',
        'js/simpleforum.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
