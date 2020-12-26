<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Bind your account');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-heading">
		<?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
	</div>
	<div class="panel-body sf-box-form">
        <?php $form = ActiveForm::begin([
		    'layout' => 'horizontal',
			'id' => 'form-auth-bind-account'
		]); ?>
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-6" style="padding-top:7px;">
				<?php echo Yii::t('app', 'You have signed in with your {name} account.', ['name'=>$authInfo['sourceName']]); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3"><?php echo Yii::t('app', 'Username'); ?></label>
			<div class="col-sm-6" style="padding-top:7px;">
				<strong><?php echo $authInfo['username']; ?></strong>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3"><?php echo Yii::t('app', 'Don\'t have an account?'); ?></label>
			<div class="col-sm-6">
				<?php echo Html::a(Yii::t('app', 'Create an account and bind it'), ['auth-signup'], ['class'=>'btn btn-primary']); ?>
			</div>
		</div>
		<br /><strong><?php echo Yii::t('app', 'Bind your account'); ?></strong><hr>
        <?php echo $form->field($model, 'username')->textInput(['maxlength'=>20]); ?>
        <?php echo $form->field($model, 'password')->passwordInput(['maxlength'=>20]); ?>
        <div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app', 'Bind'), ['class' => 'btn btn-primary', 'name' => 'login-button']); ?>
			&nbsp;&nbsp;<?php echo Html::a(Yii::t('app', 'Forgot password?'), ['site/forgot-password']); ?>
			</div>
        </div>
        <?php ActiveForm::end(); ?>
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
