<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\install_update\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\ServerErrorHttpException;
use yii\web\ForbiddenHttpException;
use app\install_update\lib\RequirementChecker;
use app\install_update\models\DatabaseForm;
use app\install_update\models\AdminSignupForm;

class InstallController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return !file_exists(Yii::getAlias('@runtime/install.lock'));
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                        throw new ForbiddenHttpException('你已安装过本程序。如确定要重装，请'."\n".'1.做好数据备份，'."\n".'2.删除runtime/install.lock文件，'."\n".'3.重新执行安装程序。');
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        if (version_compare(PHP_VERSION, '4.3', '<')) {
            echo 'php版本过低，请先安装php4.3以上版本';
            exist;
        }

        $requirementsChecker = new RequirementChecker();

        $gdMemo = $imagickMemo = '启用验证码时需要安装php的gd组件或imagick组件。';
        $gdOK = $imagickOK = false;

        if (extension_loaded('imagick')) {
            $imagick = new \Imagick();
            $imagickFormats = $imagick->queryFormats('PNG');
            if (in_array('PNG', $imagickFormats)) {
                $imagickOK = true;
            } else {
                $imagickMemo = 'Imagick组件不支持png。';
            }
        }

        if (extension_loaded('gd')) {
            $gdInfo = gd_info();
            if (!empty($gdInfo['FreeType Support'])) {
                $gdOK = true;
            } else {
                $gdMemo = 'gd组件不支持FreeType。';
            }
        }

        /**
         * Adjust requirements according to your application specifics.
         */
        $requirements = array(
            array(
                'name' => 'PHP版本',
                'mandatory' => true,
                'condition' => version_compare(PHP_VERSION, '5.4.0', '>='),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
                'memo' => 'PHP 5.4.0及以上',
            ),
            array(
                'name' => 'core/config目录写权限',
                'mandatory' => true,
                'condition' => is_writeable(Yii::getAlias('@app/config')),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => 'core/config目录需要写权限',
            ),
            array(
                'name' => 'core/runtime目录写权限',
                'mandatory' => true,
                'condition' => is_writeable(Yii::getAlias('@app/runtime')),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => 'core/runtime目录需要写权限',
            ),
            array(
                'name' => 'assets目录写权限',
                'mandatory' => true,
                'condition' => is_writeable(Yii::getAlias('@webroot/assets')),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => 'assets目录需要写权限',
            ),
            array(
                'name' => 'avatar目录写权限',
                'mandatory' => true,
                'condition' => is_writeable(Yii::getAlias('@webroot/avatar')),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => 'avatar目录需要写权限',
            ),
            array(
                'name' => 'upload目录写权限',
                'mandatory' => true,
                'condition' => is_writeable(Yii::getAlias('@webroot/upload')),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => 'upload目录需要写权限',
            ),
            // Database :
            array(
                'name' => 'PDO扩展',
                'mandatory' => true,
                'condition' => extension_loaded('pdo'),
                'by' => 'DB连接',
                'memo' => 'MySQL连接。',
            ),
            array(
                'name' => 'PDO_MySQL扩展',
                'mandatory' => true,
                'condition' => extension_loaded('pdo_mysql'),
                'by' => 'DB连接',
                'memo' => 'MySQL连接。',
            ),
            // openssl :
            array(
                'name' => 'OpenSSL扩展',
                'mandatory' => true,
                'condition' => extension_loaded('openssl'),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => '用于用户密码加密',
            ),
/*          // File Upload :
            array(
                'name' => 'FileInfo扩展',
                'mandatory' => true,
                'condition' => extension_loaded('fileinfo'),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => '用于文件上传',
            ),
*/
            // Cache :
            array(
                'name' => 'Memcache(d)扩展',
                'mandatory' => false,
                'condition' => extension_loaded('memcache') || extension_loaded('memcached'),
                'by' => 'memcache/memcached缓存',
                'memo' => '用于开启缓存',
            ),
            array(
                'name' => 'APC扩展',
                'mandatory' => false,
                'condition' => extension_loaded('apc'),
                'by' => 'APC缓存',
                'memo' => '用于开启缓存',
            ),
            // CAPTCHA:
            array(
                'name' => 'GD扩展(支持FreeType)',
                'mandatory' => false,
                'condition' => $gdOK,
                'by' => '验证码',
                'memo' => $gdMemo,
            ),
            array(
                'name' => 'ImageMagick扩展(支持png)',
                'mandatory' => false,
                'condition' => $imagickOK,
                'by' => '验证码',
                'memo' => $imagickMemo,
            ),
            // PHP ini :
            'phpExposePhp' => array(
                'name' => 'php.ini的expose_php设值',
                'mandatory' => false,
                'condition' => $requirementsChecker->checkPhpIniOff("expose_php"),
                'by' => '安全问题',
                'memo' => '请修改为 expose_php = Off',
            ),
            'phpAllowUrlInclude' => array(
                'name' => 'php.ini的allow_url_include设值',
                'mandatory' => false,
                'condition' => $requirementsChecker->checkPhpIniOff("allow_url_include"),
                'by' => '安全问题',
                'memo' => '请修改为 allow_url_include = Off',
            ),
            'phpSmtp' => array(
                'name' => 'PHP SMTP邮件',
                'mandatory' => false,
                'condition' => strlen(ini_get('SMTP'))>0,
                'by' => '邮件',
                'memo' => '用于发送邮件',
            ),
            array(
                'name' => 'MBString扩展',
                'mandatory' => true,
                'condition' => extension_loaded('mbstring'),
                'by' => '<a href="http://www.php.net/manual/en/book.mbstring.php">Multibyte string</a> processing',
                'memo' => ''
            ),
            array(
                'name' => 'Reflection扩展',
                'mandatory' => true,
                'condition' => class_exists('Reflection', false),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
            ),
            array(
                'name' => 'PCRE扩展',
                'mandatory' => true,
                'condition' => extension_loaded('pcre'),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
            ),
            array(
                'name' => 'SPL扩展',
                'mandatory' => true,
                'condition' => extension_loaded('SPL'),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
            ),
        );
        $requirementsChecker->check($requirements);
        return $this->render('index', ['check'=>$requirementsChecker]);
    }

    public function actionDbSetting()
    {
        $session = Yii::$app->getSession();
        if ( !$session->has('install-step') || $session->get('install-step') < 1 ) {
            return $this->redirect(['index']);
        }

        $model = new DatabaseForm();
        $error = false;
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            try {
                $model->excuteSql($this->module->basePath . '/data/simpleforum.sql');
                $model->createDbConfig();
                $session->set('install-step', 2);
                return $this->redirect(['create-admin']);
            } catch (\yii\db\Exception $e) {
                $error = '数据库连接出错，请确认数据库连接信息：<br />' . $e->getMessage();
            }
        }
        return $this->render('dbSetting', ['model'=>$model, 'error'=>$error]);
    }

    public function actionCreateAdmin()
    {
        $session = Yii::$app->getSession();
        if ( !$session->has('install-step') || $session->get('install-step') < 1 ) {
            return $this->redirect(['index']);
        } else if ($session->get('install-step') == 1) {
            return $this->redirect(['db-setting']);
        } else if ($session->get('install-step') == 9) {
            return $this->render('completed');
        }

        $model = new AdminSignupForm();
        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    $session->set('install-step', 9);
                    $this->createInstallLockFile();
                    return $this->render('completed');
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    private function createInstallLockFile()
    {
        return file_put_contents(Yii::getAlias('@runtime/install.lock'), '');
    }

}
