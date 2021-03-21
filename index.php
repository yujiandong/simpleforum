<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

// comment out the following two lines when deployed to production
//defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_ENV') or define('YII_ENV', 'prd');
define('WEBROOT_PATH', __DIR__);
define('SF_PATH', __DIR__.'/core');

require(SF_PATH . '/vendor/autoload.php');
require(SF_PATH . '/vendor/yiisoft/yii2/Yii.php');
require(SF_PATH . '/version.php');

$config = require(SF_PATH . '/config/web.php');

(new yii\web\Application($config))->run();
