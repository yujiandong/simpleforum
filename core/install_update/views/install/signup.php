<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app/admin', 'Create administrator\'s account');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<div class="panel panel-default sf-box">
		<div class="panel-heading">
			<?php echo Html::a(Yii::t('app/admin', 'Install SimpleForum')), '&nbsp;/&nbsp;', $this->title; ?>
		</div>
		<div class="panel-body sf-box-form">
            <?php $form = ActiveForm::begin([
			    'layout' => 'horizontal',
				'id' => 'form-admin-signup'
			]); ?>
                <?php echo $form->field($model, 'username'); ?>
                <?php echo $form->field($model, 'name'); ?>
                <?php echo $form->field($model, 'email'); ?>
                <?php echo $form->field($model, 'password')->passwordInput(); ?>
                <?php echo $form->field($model, 'password_repeat')->passwordInput(); ?>
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
<!-- sf-right end -->

</div>
