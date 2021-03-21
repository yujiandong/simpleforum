<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Alert;

$this->title = Yii::t('app/admin', 'Test Email Sending');

?>

<div class="row">
<div class="col-lg-8 sf-left">

<div class="card sf-box">
    <div class="card-header sf-box-header sf-navi">
        <?php echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;›&nbsp;', $this->title; ?>
    </div>
    <div class="card-body sf-box-form">
<?php
if ( $rtnCd === 9 ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => Yii::t('app/admin', 'An error has occurred with sending a test email. Please check {link}.', ['link'=>Html::a(Yii::t('app/admin', 'SMTP Settings'), ['admin/setting/update', '#'=>'mailer'])])
               . '<br />' . $msg,
        ]);
} else if ( $rtnCd === 1 ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => Yii::t('app/admin', 'A test email has been successfully sent. Please check your email.'),
        ]);
}
?>
<?php $form = ActiveForm::begin([
            'action' => ['admin/setting/test-email'],
            'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-form-label col-sm-3 text-sm-right',
                    'wrapper' => 'col-sm-9',
                ],
            ],
        ]); ?>
        <?php echo $form->field($model, 'email')->textInput(['maxlength'=>50]); ?>
        <?php echo $form->field($model, 'content')->textArea(['maxlength'=>255]); ?>
        <div class="form-group">
            <div class="offset-sm-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app/admin', 'Test'), ['class' => 'btn sf-btn']); ?>
            </div>
        </div>
<?php ActiveForm::end(); ?>
    </div>
</div>

</div>

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>

</div>
