<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = Yii::t('app', 'Buy Invite Codes');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-lg-8 sf-left">

<div class="card sf-box">
    <div class="card-header sf-box-header sf-navi">
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="card-body sf-box-form">
<?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => 'form-buy-invite-codes',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-form-label col-sm-3 text-sm-right',
                    'wrapper' => 'col-sm-9',
                ],
            ],
       ]); ?>
            <?php echo $form->field($model, 'amount')->textInput(['maxlength'=>3]); ?>
            <?php echo $form->field($model, 'password')->passwordInput(['maxlength'=>20]); ?>
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
<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
