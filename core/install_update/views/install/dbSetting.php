<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

$this->title = Yii::t('app/admin', 'MySQL setting');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
    <div class="panel panel-default sf-box">
        <div class="panel-heading">
            <?php echo Html::a(Yii::t('app/admin', 'Install SimpleForum'), ['index']), '&nbsp;/&nbsp;', $this->title; ?>
        </div>
        <div class="panel-body sf-box-form">
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
                'id' => 'form-dbinfo'
            ]); ?>
                <?php echo $form->field($model, 'host'); ?>
                <?php echo $form->field($model, 'port'); ?>
                <?php echo $form->field($model, 'dbname'); ?>
                <?php echo $form->field($model, 'username'); ?>
                <?php echo $form->field($model, 'password'); ?>
                <?php echo $form->field($model, 'tablePrefix'); ?>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                    <?php echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary', 'name' => 'dbsetting-button']); ?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<!-- sf-left end -->

</div>
