<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\Alert;

$this->title = '每日签到';
$me = Yii::$app->getUser()->getIdentity();
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="panel-body">
<?php
    $continue = $me->checkTodaySigned();
    if ($continue === false) {
        echo Html::a('<i class="fa fa-gift" aria-hidden="true"></i>请点击签到', Yii::$app->getRequest()->url, [
            'title' => '签到',
            'class' => 'btn btn-primary',
            'data' => [
                'method' => 'post',
            ]]);
    } else {
        echo '<p class="gray">今日登录奖励已领取，已连续登录 <strong>'.$continue.'</strong> 天</p>', Html::a('查看我的账户余额', ['my/balance'], ['class' => 'btn btn-primary']);
    }
?>
    </div>
</div>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
