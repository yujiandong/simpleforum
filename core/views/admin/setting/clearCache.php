<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

$this->title = Yii::t('app/admin', 'Clear cache');

?>

<div class="row">
<div class="col-lg-8 sf-left">

<div class="card sf-box">
    <div class="card-header sf-box-header sf-navi">
        <?php echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;›&nbsp;', $this->title; ?>
    </div>
    <div class="card-body sf-box-form">
        <?php echo Yii::t('app/admin', 'Cache is cleared.'); ?>
    </div>
</div>

</div>

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>

</div>
