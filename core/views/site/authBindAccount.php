<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '绑定本站帐号';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-heading">
		<?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
	</div>
	<div class="panel-body sf-box-form">
        <?php $form = ActiveForm::begin([
		    'layout' => 'horizontal',
			'id' => 'form-auth-bind-account'
		]); ?>
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-6" style="padding-top:7px;">
				您已通过<?php echo $authInfo['sourceName']; ?>登录
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">用户名</label>
			<div class="col-sm-6" style="padding-top:7px;">
				<strong><?php echo $authInfo['username']; ?></strong>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">还没本站帐号</label>
			<div class="col-sm-6">
				<?php echo Html::a('创建本站帐号并绑定', ['auth-signup'], ['class'=>'btn btn-primary']); ?>
			</div>
		</div>
		<br /><strong>绑定本站账号</strong><hr>
        <?php echo $form->field($model, 'username')->textInput(['maxlength'=>20]); ?>
        <?php echo $form->field($model, 'password')->passwordInput(['maxlength'=>20]); ?>
        <div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('绑定', ['class' => 'btn btn-primary', 'name' => 'login-button']); ?>
			&nbsp;&nbsp;<?php echo Html::a('忘记密码了', ['site/forgot-password']); ?>
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
