<?php

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    echo 'php版本过低，请先安装php5.4.0以上版本';
    exit;
}

if (file_exists(__DIR__.'/../runtime/install.lock')) {
    echo '你已安装过本程序。如确定要重装，请'."\n".'1.做好数据备份，'."\n".'2.删除runtime/install.lock文件，'."\n".'3.重新执行安装程序。';
    exit;
}

if ( !is_writeable(__DIR__.'/../config') ) {
    echo 'config目录没有写权限。请加上写权限。';
    exit;
}

if ( !is_writeable(__DIR__.'/assets') ) {
    echo 'web/assets目录没有写权限。请加上写权限。';
    exit;
}

if ( !is_writeable(__DIR__.'/../runtime') ) {
    echo 'runtime目录没有写权限。请加上写权限。';
    exit;
}

if (!file_exists(__DIR__.'/../config/db.php')) {
    if (!copy(__DIR__.'/../config/db.php.default', __DIR__.'/../config/db.php')) {
        echo "文件copy出错，请手动将config目录下db.php.default改名为db.php";
        exit;
    }
}

if (!file_exists(__DIR__.'/../config/params.php')) {
    if (!copy(__DIR__.'/../config/params.php.default', __DIR__.'/../config/params.php')) {
        echo "文件copy出错，请手动将config目录下params.php.default改名为params.php";
        exit;
    }
}

header("Location: /install");
exit;

?>
