<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\assets;

use yii\web\AssetBundle;

class WysibbAsset extends AssetBundle
{
//    public $basePath = '@webroot';
    public $baseUrl = '@web/static';
    public $css = [
        'assets/wysibb/theme/default/wbbtheme.css',
    ];
    public $js = [
        'assets/wysibb/jquery.wysibb.min.js',
        'assets/wysibb/lang/cn.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
