<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\Alert;

$session = Yii::$app->getSession();

$this->title = Yii::t('app', 'Error occurred');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="panel-body">
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
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
