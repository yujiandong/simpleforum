<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '登录';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<div class="box">
		<div class="inner">
			<?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
		</div>
		<div class="cell cell-form">
            <?php $form = ActiveForm::begin([
			    'layout' => 'horizontal',
				'id' => 'form-login'
			]); ?>
            <?php echo $form->field($model, 'username')->textInput(['maxlength'=>20]); ?>
            <?php echo $form->field($model, 'password')->passwordInput(['maxlength'=>20]); ?>
			<?php echo $form->field($model, 'rememberMe')->checkbox(); ?>
<?php
	if ( intval(Yii::$app->params['settings']['captcha_enabled']) === 1 ) {
		echo $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname());
	}
?>
            <div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
                <?php echo Html::submitButton('登录', ['class' => 'btn btn-primary', 'name' => 'login-button']); ?>
				&nbsp;&nbsp;<?php echo Html::a('忘记密码了', ['site/forgot-password']); ?>
				</div>
            </div>
            <?php ActiveForm::end(); ?>
<?php if ( intval(Yii::$app->params['settings']['auth_enabled']) === 1 ) : ?>
			<h6 class="login-three-home"><strong>第三方账号直接登录</strong></h6>
			<?php echo
			\yii\authclient\widgets\AuthChoice::widget([
			    'baseAuthUrl' => ['site/auth'],
			    'popupMode' => false,
			]);
			?>
<?php endif; ?>
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
