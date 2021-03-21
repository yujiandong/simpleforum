<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

$session = Yii::$app->getSession();

$this->title = Yii::t('app', 'Error occurred');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-lg-8 sf-left">

<div class="card sf-box">
    <div class="card-header sf-box-header sf-navi">
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="card-body">
        <p><strong><?php echo Html::encode($name); ?></strong></p>
        <div class="alert alert-danger">
            <?php echo nl2br(Html::encode($message)); ?>
        </div>
        <p><?php echo Yii::t('app', 'The above error occurred while the Web server was processing your request.'); ?></p>
        <p><?php echo Yii::t('app', 'Please contact administrator if you think this is a server error. Thank you.'); ?></p>
    </div>
</div>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
