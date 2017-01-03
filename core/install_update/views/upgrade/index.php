<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '极简论坛升级';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
<ul class="list-group sf-box">
    <li class="list-group-item">
        <?php echo $this->title; ?>
    </li>
    <li class="list-group-item list-group-item-info"><strong>升级前确认</strong></li>
    <li class="list-group-item">
        <p>请先备份网站程序，并备份数据库数据。本站不对升级过程中造成的程序或数据损失等负责。</p>
        <?php
            echo Html::a('已备份，开始升级', ['v115to120'], ['class'=>'btn btn-primary']);
        ?>
    </li>
</ul>
</div>
<!-- sf-left end -->

</div>
