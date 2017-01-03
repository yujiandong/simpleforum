<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

$items = [
    '配置管理'=>['admin/setting'],
    '第三方登录管理'=>['admin/setting/auth'],
    '节点管理'=>['admin/node'],
    '导航管理'=>['admin/navi'],
    '用户管理'=>['admin/user'],
    '标签管理'=>['admin/tag'],
    '积分管理'=>['admin/user/charge'],
    '链接管理'=>['admin/link'],
    '插件管理'=>['admin/plugin'],
    '邮件测试'=>['admin/setting/test-email'],
    '清空缓存'=>['admin/setting/clear-cache'],
];

?>

<div class="panel panel-default sf-box">
    <div class="panel-heading gray">论坛管理</div>
    <div class="panel-body sf-btn">
<?php
    foreach($items as $k=>$v) {
        echo Html::a($k, $v, ['class'=>'btn btn-default']);
    }
?>
    </div>
</div>
