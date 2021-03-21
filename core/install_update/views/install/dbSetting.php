<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Alert;

$this->title = Yii::t('app/admin', 'MySQL setting');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-lg-8 sf-left">
    <div class="card sf-box">
        <div class="card-header bg-transparent">
            <?php echo Html::a(Yii::t('app/admin', 'Install SimpleForum'), ['index']), '&nbsp;/&nbsp;', $this->title; ?>
        </div>
        <div class="card-body sf-box-form">
<?php
if ( !empty($error) ) {
    echo Alert::widget([
       'options' => ['class' => 'alert-danger'],
       'closeButton'=>false,
       'body' => $error,
    ]);
}
?>
<?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => 'form-dbinfo',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-form-label col-sm-3 text-sm-right',
                    'wrapper' => 'col-sm-9',
                ],
            ],
       ]); ?>
                <?php echo $form->field($model, 'host'); ?>
                <?php echo $form->field($model, 'port'); ?>
                <?php echo $form->field($model, 'dbname'); ?>
                <?php echo $form->field($model, 'username'); ?>
                <?php echo $form->field($model, 'password'); ?>
                <?php echo $form->field($model, 'tablePrefix'); ?>
                <div class="form-group">
                    <div class="offset-sm-3 col-sm-9">
                    <?php echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary', 'name' => 'dbsetting-button']); ?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<!-- sf-left end -->

</div>
