<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '论坛管理';

$items = [
    '配置管理'=>['admin/setting'],
    '第三方登录管理'=>['admin/setting/auth'],
    '节点管理'=>['admin/node'],
    '导航管理'=>['admin/navi'],
    '用户管理'=>['admin/user'],
    '链接管理'=>['admin/link'],
    '插件管理'=>['admin/plugin'],
    '邮件测试'=>['admin/setting/test-email'],
    '清空缓存'=>['admin/setting/clear-cache'],
];

?>
<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading"><?php echo $this->title; ?></div>
    <div class="panel-body sf-btn">
<?php
    foreach($items as $k=>$v) {
        echo Html::a($k, $v, ['class'=>'btn btn-default']);
    }
?>
    </div>
</div>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->

</div>
