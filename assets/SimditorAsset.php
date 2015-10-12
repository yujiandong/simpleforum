<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\assets;

use yii\web\AssetBundle;

class SimditorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'assets/simditor-2.2.4/styles/simditor.css',
    ];
    public $js = [
        'assets/simditor-2.2.4/scripts/module.min.js',
        'assets/simditor-2.2.4/scripts/hotkeys.min.js',
        'assets/simditor-2.2.4/scripts/simditor.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
