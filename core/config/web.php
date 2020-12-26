<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\ArrayHelper;

$params = require(__DIR__ . '/params.php');
$params += require(__DIR__ . '/plugins.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'app\components\SfBootstrap', 'app\components\LanguageSelector'],
    'language' => ArrayHelper::remove($params['settings'], 'language', 'en-US'),
    'timeZone' => ArrayHelper::remove($params['settings'], 'timezone', 'UTC'),
    'defaultRoute' => 'topic/index',
    'components' => [
        'request' => [
            'cookieValidationKey' => 'hwdn8-iyIh5LylPLpD1PoplqjUka98Ba',
        ],
	    'i18n' => [
	        'translations' => [
	            'app*' => [
	                'class' => 'yii\i18n\PhpMessageSource',
	                //'basePath' => '@app/messages',
	                //'sourceLanguage' => 'en-US',
	                'fileMap' => [
	                    'app' => 'app.php',
	                    'app/admin' => 'admin.php',
	                ],
	            ],
	        ],
	    ],
        'cache' =>  [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require(__DIR__ . '/urlrules.php'),
        ],
        'assetManager' => [
            'basePath' => WEBROOT_PATH . '/assets',
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,
                    'baseUrl' => '@web/static',
                    'js' => [
                    'js/jquery-1.12.2.min.js',
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => null,
                    'baseUrl' => '@web/static',
                    'css' => [
                        'assets/bootstrap/bootstrap.min.css',
                    ]
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'sourcePath' => null,
                    'baseUrl' => '@web/static',
                    'js' => [
                        'assets/bootstrap/bootstrap.min.js',
                    ]
                ],
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
        ],

    ],
    'params' => $params,
];

$setting = ArrayHelper::remove($params, 'settings', []);
$plugins = ArrayHelper::remove($params, 'plugins', []);

//cache
if( intval($setting['cache_enabled']) !== 0 && intval($setting['cache_time'])>0 && !empty($setting['cache_info']) ) {
    $config['components']['cache'] = $setting['cache_info'];
}

//theme
if( isset($setting['theme']) && !empty($setting['theme']) && file_exists(dirname(__DIR__). '/themes/' . $setting['theme']) ) {
    $config['components']['view'] = [
        'theme' => [
            'basePath' => '@app/themes/'.$setting['theme'],
            'baseUrl' => '@web/static/themes/'.$setting['theme'],
            'pathMap' => [
                '@app/views' => '@app/themes/'.$setting['theme'],
            ],
        ],
    ];
}

//mailer
if( !empty($setting['mailer_host']) && intval($setting['mailer_port'])>0 && !empty($setting['mailer_username']) && !empty($setting['mailer_password']) ) {
    $config['components']['mailer']['transport'] = [
        'class' => 'Swift_SmtpTransport',
        'host' => $setting['mailer_host'],
        'port' => $setting['mailer_port'],
        'encryption' => $setting['mailer_encryption'],
        'username' => $setting['mailer_username'],
        'password' => $setting['mailer_password'],
    ];
}

if ( intval($setting['auth_enabled']) !== 0 && !empty($setting['auth_setting']) ) {
    $authClass = [
        'qq' => 'yujiandong\authclient\Qq',
        'weibo' => 'yujiandong\authclient\Weibo',
        'weixin' => 'yujiandong\authclient\Weixin',
        'weixinmp' => 'yujiandong\authclient\Weixin',
        'github' => 'yujiandong\authclient\GitHub',
        'facebook' => 'yii\authclient\clients\Facebook',
        'twitter' => 'yii\authclient\clients\Twitter',
    ];

    foreach($setting['auth_setting'] as $type=>$auth) {
        $client = [
            'class' => $authClass[$type],
            'clientId' => $auth['clientId'],
            'clientSecret' => $auth['clientSecret'],
            'title' => $auth['title'],
        ];
        if($type === 'weixinmp') {
            $client['type'] = 'mp';
        }
        $config['components']['authClientCollection']['clients'][$type] = $client;
    }
}

//alias
if( !empty($setting['alias_static']) ) {
    $config['aliases']['@web/static'] = $setting['alias_static'];
}
if( !empty($setting['alias_avatar']) ) {
    $config['aliases']['@web/avatar'] = $setting['alias_avatar'];
} else if( $setting['upload_avatar'] === 'remote' && !empty($plugins[$setting['upload_remote']]) )  {
    $config['aliases']['@web/avatar'] = $plugins[$setting['upload_remote']]['url'].'/avatar';
}
if( !empty($setting['alias_upload']) ) {
    $config['aliases']['@web/upload'] = $setting['alias_upload'];
}
if( !empty($setting['alias_runtime']) ) {
    $config['aliases']['@runtime'] = $setting['alias_runtime'];
}

if( $setting['upload_avatar'] === 'remote' && !empty($setting['upload_remote']) && isset($plugins[$setting['upload_remote']]) ) {
    \Yii::$container->set('avatarUploader', $plugins[$setting['upload_remote']]);
} else {
    \Yii::$container->set('avatarUploader', 'app\components\Upload');
}
if( $setting['upload_file'] === 'remote' && !empty($setting['upload_remote']) && isset($plugins[$setting['upload_remote']]) ) {
    \Yii::$container->set('fileUploader', $plugins[$setting['upload_remote']]);
} else {
    \Yii::$container->set('fileUploader', 'app\components\Upload');
}

if (file_exists(dirname(__DIR__). '/install_update')) { 
    $config['bootstrap'][] = 'install_update';
    $config['modules']['install_update'] = 'app\install_update\Module';
}

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['127.0.0.1','192.168.0.*', '111.96.222.7', '::1']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}
return $config;
