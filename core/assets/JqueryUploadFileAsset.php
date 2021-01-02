<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\assets;

use yii\web\AssetBundle;

class JqueryUploadFileAsset extends AssetBundle
{
//    public $basePath = '@webroot';
    public $baseUrl = '@web/static';
    public $css = [
        'assets/jquery-upload-file/jquery-file-upload.min.css',
    ];
    public $js = [
        'assets/jquery-upload-file/jquery-file-upload.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
