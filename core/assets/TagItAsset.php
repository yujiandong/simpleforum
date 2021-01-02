<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\assets;

use yii\web\AssetBundle;

class TagItAsset extends AssetBundle
{
//    public $basePath = '@webroot';
    public $baseUrl = '@web/static';
    public $css = [
        'assets/jquery-ui-1.12.1/jquery-ui.min.css',
        'assets/tag-it/jquery.tagit.css',
    ];
    public $js = [
        'assets/jquery-ui-1.12.1/jquery-ui.min.js',
        'assets/tag-it/tag-it.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
