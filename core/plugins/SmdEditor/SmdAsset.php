<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\plugins\SmdEditor;

use yii\web\AssetBundle;

class SmdAsset extends AssetBundle
{
//    public $basePath = '@webroot';
    public $baseUrl = '@web/static/assets/smd';
    public $css = [
        'smd.css',
    ];
    public $js = [
        'lib/jquery.selection.js',
        'lib/marked.js',
        'smd.js',
        'smd-for-simpleforum.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
