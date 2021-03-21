<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

$this->title = Yii::t('app/admin', 'Upgrade SimpleForum');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-lg-8 sf-left">
<ul class="list-group sf-box">
    <li class="list-group-item">
        <?php echo $this->title; ?>
    </li>
    <li class="list-group-item list-group-item-info"><strong><?php echo Yii::t('app/admin', 'Confirm befor upgrade'); ?></strong></li>
    <li class="list-group-item">
        <p><?php echo Yii::t('app/admin', 'Befor upgrading your SimpleForum, make sure that you back up your database.'); ?></p>
        <?php
            echo Html::a(Yii::t('app/admin', 'Backup completed, Start upgrade'), ['v115to120'], ['class'=>'btn btn-primary']);
        ?>
    </li>
</ul>
</div>
<!-- sf-left end -->

</div>
