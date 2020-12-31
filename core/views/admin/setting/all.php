<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app/admin', 'Forum Manager');

$items = [
    'Settings'=>['admin/setting'],
    'Third-party login'=>['admin/setting/auth'],
    'Nodes'=>['admin/node'],
    'Navigations'=>['admin/navi'],
    'Members'=>['admin/user'],
    'Tags'=>['admin/tag'],
    'Points'=>['admin/user/charge'],
    'Links'=>['admin/link'],
    'Plugins'=>['admin/plugin'],
    'Test email'=>['admin/setting/test-email'],
    'Clear cache'=>['admin/setting/clear-cache'],
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
        echo Html::a(Yii::t('app/admin', $k), $v, ['class'=>'btn btn-default']);
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
