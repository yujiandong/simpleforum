<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = Yii::t('app/admin', 'Create administrator\'s account');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-lg-8 sf-left">
    <div class="card sf-box">
        <div class="card-header bg-transparent">
            <?php echo Html::a(Yii::t('app/admin', 'Install SimpleForum')), '&nbsp;/&nbsp;', $this->title; ?>
        </div>
        <div class="card-body sf-box-form">
<?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => 'form-admin-signup',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-form-label col-sm-3 text-sm-right',
                    'wrapper' => 'col-sm-9',
                ],
            ],
       ]); ?>
                <?php echo $form->field($model, 'username'); ?>
                <?php echo $form->field($model, 'name'); ?>
                <?php echo $form->field($model, 'email'); ?>
                <?php echo $form->field($model, 'password')->passwordInput(); ?>
                <?php echo $form->field($model, 'password_repeat')->passwordInput(); ?>
                <div class="form-group">
                    <div class="offset-sm-3 col-sm-9">
                    <?php echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn sf-btn']); ?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<!-- sf-left end -->

<!-- sf-right start -->
<!-- sf-right end -->

</div>
