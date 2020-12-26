<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
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
                        throw new ForbiddenHttpException(Yii::t('app/admin', 'SimpleForum was already installed. If you want to reinstall it, please ')
                          ."\n".Yii::t('app/admin', '1. Backup your database')
                          ."\n".Yii::t('app/admin', '2. Delete \'runtime/install.lock\' file')
                          ."\n".Yii::t('app/admin', '3. Retry installation')
                        );
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
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            echo Yii::t('app/admin', 'PHP 5.4.0 or higher is required.');
            exist;
        }

        $requirementsChecker = new RequirementChecker();

        $gdMemo = $imagickMemo = Yii::t('app/admin', 'GD or ImageMagick extension is equired for captcha.');
        $gdOK = $imagickOK = false;

        if (extension_loaded('imagick')) {
            $imagick = new \Imagick();
            $imagickFormats = $imagick->queryFormats('PNG');
            if (in_array('PNG', $imagickFormats)) {
                $imagickOK = true;
            } else {
                $imagickMemo = Yii::t('app/admin', 'Imagick extension does not support PNG.');
            }
        }

        if (extension_loaded('gd')) {
            $gdInfo = gd_info();
            if (!empty($gdInfo['FreeType Support'])) {
                $gdOK = true;
            } else {
                $gdMemo = Yii::t('app/admin', 'GD extension does not support FreeType.');
            }
        }

        /**
         * Adjust requirements according to your application specifics.
         */
        $requirements = array(
            array(
                'name' => Yii::t('app/admin', 'PHP version'),
                'mandatory' => true,
                'condition' => version_compare(PHP_VERSION, '5.4.0', '>='),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
                'memo' => Yii::t('app/admin', 'PHP 5.4.0 or higher is required.'),
            ),
            array(
                'name' => Yii::t('app/admin', '\'{folder}\' folder is writable', ['folder' => 'core/config']),
                'mandatory' => true,
                'condition' => is_writeable(Yii::getAlias('@app/config')),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => Yii::t('app/admin', 'Write permissions is required.'),
            ),
            array(
                'name' => Yii::t('app/admin', '\'{folder}\' folder is writable', ['folder' => 'core/runtime']),
                'mandatory' => true,
                'condition' => is_writeable(Yii::getAlias('@app/runtime')),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => Yii::t('app/admin', 'Write permissions is required.'),
            ),
            array(
                'name' => Yii::t('app/admin', '\'{folder}\' folder is writable', ['folder' => 'assets']),
                'mandatory' => true,
                'condition' => is_writeable(Yii::getAlias('@webroot/assets')),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => Yii::t('app/admin', 'Write permissions is required.'),
            ),
            array(
                'name' => Yii::t('app/admin', '\'{folder}\' folder is writable', ['folder' => 'avatar']),
                'mandatory' => true,
                'condition' => is_writeable(Yii::getAlias('@webroot/avatar')),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => Yii::t('app/admin', 'Write permissions is required.'),
            ),
            array(
                'name' => Yii::t('app/admin', '\'{folder}\' folder is writable', ['folder' => 'upload']),
                'mandatory' => true,
                'condition' => is_writeable(Yii::getAlias('@webroot/upload')),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => Yii::t('app/admin', 'Write permissions is required.'),
            ),
            // Database :
            array(
                'name' => Yii::t('app/admin', '{extension} extension', ['extension' => 'PDO']),
                'mandatory' => true,
                'condition' => extension_loaded('pdo'),
                'by' => Yii::t('app/admin', 'DB connection'),
                'memo' => Yii::t('app/admin', 'Required for MySQL connection.'),
            ),
            array(
                'name' => Yii::t('app/admin', '{extension} extension', ['extension' => 'PDO_MySQL']),
                'mandatory' => true,
                'condition' => extension_loaded('pdo_mysql'),
                'by' => Yii::t('app/admin', 'DB connection'),
                'memo' => Yii::t('app/admin', 'Required for MySQL connection.'),
            ),
            // openssl :
            array(
                'name' => Yii::t('app/admin', '{extension} extension', ['extension' => 'OpenSSL']),
                'mandatory' => true,
                'condition' => extension_loaded('openssl'),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => Yii::t('app/admin', 'Required by encrypt and decrypt methods.'),
            ),
/*          // File Upload :
            array(
                'name' => Yii::t('app/admin', '{extension} extension', ['extension' => 'FileInfo']),
                'mandatory' => true,
                'condition' => extension_loaded('fileinfo'),
                'by' => '<a href="http://simpleforum.org">Simple Forum</a>',
                'memo' => Yii::t('app/admin', 'Required for files upload to detect correct file mime-types.'),
            ),
*/
            // Cache :
            array(
                'name' => Yii::t('app/admin', '{extension} extension', ['extension' => 'Memcache(d)']),
                'mandatory' => false,
                'condition' => extension_loaded('memcache') || extension_loaded('memcached'),
                'memo' => Yii::t('app/admin', 'Required for cache.'),
            ),
            array(
                'name' => Yii::t('app/admin', '{extension} extension', ['extension' => 'APC']),
                'mandatory' => false,
                'condition' => extension_loaded('apc'),
                'memo' => Yii::t('app/admin', 'Required for cache.'),
            ),
            // CAPTCHA:
            array(
                'name' => Yii::t('app/admin', 'GD extension(support FreeType)'),
                'mandatory' => false,
                'condition' => $gdOK,
                'memo' => $gdMemo,
            ),
            array(
                'name' => Yii::t('app/admin', 'Imagick extension(support png)'),
                'mandatory' => false,
                'condition' => $imagickOK,
                'memo' => $imagickMemo,
            ),
            // PHP ini :
            'phpExposePhp' => array(
                'name' => Yii::t('app/admin', 'expose_php setting in the php.ini'),
                'mandatory' => false,
                'condition' => $requirementsChecker->checkPhpIniOff("expose_php"),
                'memo' => Yii::t('app/admin', 'Please change to \'expose_php = Off\''),
            ),
            'phpAllowUrlInclude' => array(
                'name' => Yii::t('app/admin', 'allow_url_include setting in the php.ini'),
                'mandatory' => false,
                'condition' => $requirementsChecker->checkPhpIniOff("allow_url_include"),
                'memo' => Yii::t('app/admin', 'Please change to \'allow_url_include = Off\''),
            ),
            'phpSmtp' => array(
                'name' => Yii::t('app/admin', 'PHP SMPT mail'),
                'mandatory' => false,
                'condition' => strlen(ini_get('SMTP'))>0,
                'memo' => Yii::t('app/admin', 'Required for sending email.'),
            ),
            array(
                'name' => Yii::t('app/admin', '{extension} extension', ['extension' => 'MBString']),
                'mandatory' => true,
                'condition' => extension_loaded('mbstring'),
                'by' => '<a href="http://www.php.net/manual/en/book.mbstring.php">Multibyte string</a> processing',
            ),
            array(
                'name' => Yii::t('app/admin', '{extension} extension', ['extension' => 'Reflection']),
                'mandatory' => true,
                'condition' => class_exists('Reflection', false),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
            ),
            array(
                'name' => Yii::t('app/admin', '{extension} extension', ['extension' => 'PCRE']),
                'mandatory' => true,
                'condition' => extension_loaded('pcre'),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
            ),
            array(
                'name' => Yii::t('app/admin', '{extension} extension', ['extension' => 'SPL']),
                'mandatory' => true,
                'condition' => extension_loaded('SPL'),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
            ),
            array(
                'name' => Yii::t('app/admin', '{extension} extension', ['extension' => 'ctype']),
                'mandatory' => true,
                'condition' => extension_loaded('ctype'),
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
                $error = Yii::t('app/admin', 'Error establishing a database connection: Please confirm database settings.') . '<br />' . $e->getMessage();
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
