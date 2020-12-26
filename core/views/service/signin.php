<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\Alert;

$this->title = Yii::t('app', 'Daily Bonus');
$me = Yii::$app->getUser()->getIdentity();
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="panel-body">
<?php
    $continue = $me->checkTodaySigned();
    if ($continue === false) {
        echo Html::a('<i class="fa fa-gift" aria-hidden="true"></i>' . Yii::t('app', 'Get today\'s bonus'), Yii::$app->getRequest()->url, [
            'title' => Yii::t('app', 'Get today\'s bonus'),
            'class' => 'btn btn-primary',
            'data' => [
                'method' => 'post',
            ]]);
    } else {
        echo '<p class="gray">', Yii::t('app', 'You have got today\'s bonus. You have got bonus for {n, plural, =1{<strong>#</strong> day} other{consecutive <strong>#</strong> days}}.', ['n' => $continue]) , '</p>', Html::a(Yii::t('app', 'My Points'), ['my/balance'], ['class' => 'btn btn-primary']);
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
