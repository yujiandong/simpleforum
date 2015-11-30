<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\assets;

use yii\web\AssetBundle;

class JqueryUploadFileAsset extends AssetBundle
{
//    public $basePath = '@webroot';
    public $baseUrl = '@web/static';
    public $css = [
        'assets/jquery-upload-file/css/uploadfile.css',
    ];
    public $js = [
        'assets/jquery-upload-file/js/jquery.uploadfile.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
