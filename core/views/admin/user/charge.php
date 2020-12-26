<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\components\SfHtml;

$session = Yii::$app->getSession();
$this->title = Yii::t('app', 'Charge Points');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <?php echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="panel-body sf-box-form">
<?php
if ( $session->hasFlash('ChargePointNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => Yii::t('app', $session->getFlash('ChargePointNG')),
        ]);
} else if ( $session->hasFlash('ChargePointOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => Yii::t('app', $session->getFlash('ChargePointOK')),
        ]);
}
?>
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => 'form-charge'
        ]); ?>
            <?php echo $form->field($model, 'username')->textInput(['maxlength'=>16]); ?>
            <?php echo $form->field($model, 'point')->textInput(['maxlength'=>8]); ?>
            <?php echo $form->field($model, 'msg')->textarea(['rows' => 3, 'maxlength'=>255]); ?>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                <?php echo Html::submitButton(Yii::t('app/admin', 'Submit'), ['class' => 'btn btn-primary']); ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
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
