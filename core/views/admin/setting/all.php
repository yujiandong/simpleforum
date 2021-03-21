<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

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
<div class="col-lg-8 sf-left">

<div class="card sf-box">
    <div class="card-header sf-box-header sf-navi"><?php echo $this->title; ?></div>
    <div class="card-body sf-links">
<?php
    foreach($items as $k=>$v) {
        echo Html::a(Yii::t('app/admin', $k), $v, ['class'=>'btn']);
    }
?>
    </div>
</div>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->

</div>
