<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

define('SF_DIR', 'core');
define('SF_PATH', __DIR__.'/' . SF_DIR);

header("Content-Type: text/html; charset=UTF-8");

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    echo 'php版本过低，请先安装php5.4.0以上版本';
    exit;
}

if (file_exists(SF_PATH . '/runtime/install.lock')) {
    echo '你已安装过本程序。如确定要重装，请'."\n".'1.做好数据备份，'."\n".'2.删除' . SF_DIR . '/runtime/install.lock文件，'."\n".'3.重新执行安装程序。';
    exit;
}

if ( !is_writeable(SF_PATH . '/config') ) {
    echo SF_DIR . '/config目录没有写权限。请加上写权限。';
    exit;
}

if ( !is_writeable(__DIR__.'/assets') ) {
    echo 'assets目录没有写权限。请加上写权限。';
    exit;
}

if ( !is_writeable(SF_PATH . '/runtime') ) {
    echo SF_DIR . '/runtime目录没有写权限。请加上写权限。';
    exit;
}

if ( !is_writeable(__DIR__.'/avatar') ) {
    echo 'avatar目录没有写权限。请加上写权限。';
    exit;
}

if ( !is_writeable(__DIR__.'/upload') ) {
    echo 'upload目录没有写权限。请加上写权限。';
    exit;
}

if (!file_exists(SF_PATH . '/config/db.php')) {
    if (!copy(SF_PATH . '/config/db.php.default', SF_PATH . '/config/db.php')) {
        echo '文件copy出错，请手动将' . SF_DIR . '/config目录下db.php.default改名为db.php';
        exit;
    }
}

if (!file_exists(SF_PATH . '/config/params.php')) {
    if (!copy(SF_PATH . '/config/params.php.default', SF_PATH . '/config/params.php')) {
        echo '文件copy出错，请手动将' . SF_DIR . '/config目录下params.php.default改名为params.php';
        exit;
    }
}

if (!file_exists(SF_PATH . '/config/plugins.php')) {
    if (!copy(SF_PATH . '/config/plugins.php.default', SF_PATH . '/config/plugins.php')) {
        echo '文件copy出错，请手动将' . SF_DIR . '/config目录下plugins.php.default改名为plugins.php';
        exit;
    }
}

header("Location: install");

?>
