<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

$params = require(__DIR__ . '/params.php');

$baseUrl = (new yii\web\Request)->getBaseUrl();
$assetBaseUrl = '';
if ($baseUrl != '') {
    $baseUrl = str_replace('/'.WEBROOT_DIR, '', $baseUrl);
    $assetBaseUrl = substr($baseUrl, 1);
}

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'bootstrap' => ['log', 'app\lib\AppBootstrap'],
    'timeZone' => 'Asia/Shanghai',
    'defaultRoute' => 'topic/index',
    'components' => [
        'request' => [
            'baseUrl' => $baseUrl,
            'cookieValidationKey' => 'hwdn8-iyIh5LylPLpD1PoplqjUka98Ba',
        ],
        'cache' => [
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
            'baseUrl' => $baseUrl,
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

$setting = $params['settings'];
unset($params);

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

if ( intval($setting['auth_enabled']) !== 0 ) {
    // qq login
    if( !empty($setting['qq_appid']) && !empty($setting['qq_appkey']) ) {
        $config['components']['authClientCollection']['clients']['qq'] = [
            'class' => 'yujiandong\authclient\Qq',
            'clientId' => $setting['qq_appid'],
            'clientSecret' => $setting['qq_appkey'],
            'title' => 'QQ登录',
        ];
    }
    // weibo login
    if( !empty($setting['wb_key']) && !empty($setting['wb_secret']) ) {
        $config['components']['authClientCollection']['clients']['weibo'] = [
            'class' => 'yujiandong\authclient\Weibo',
            'clientId' => $setting['wb_key'],
            'clientSecret' => $setting['wb_secret'],
            'title' => '微博登录',
        ];
    }
}
//timezone
if( !empty($setting['timezone']) ) {
    $config['timeZone'] = $setting['timezone'];
}

//alias
if( !empty($setting['alias_static']) ) {
    $config['aliases']['@web/static'] = $setting['alias_static'];
}
if( !empty($setting['alias_avatar']) ) {
    $config['aliases']['@web/avatar'] = $setting['alias_avatar'];
} else if( $setting['upload_avatar'] === 'remote' && !empty($setting['upload_remote_url']) )  {
    $config['aliases']['@web/avatar'] = $setting['upload_remote_url'].'/avatar';
}
if( !empty($setting['alias_upload']) ) {
    $config['aliases']['@web/upload'] = $setting['alias_upload'];
}
if( !empty($setting['alias_runtime']) ) {
    $config['aliases']['@runtime'] = $setting['alias_runtime'];
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
