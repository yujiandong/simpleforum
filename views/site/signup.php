<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\SignupForm;

if ($model->action === SignupForm::ACTION_AUTH_SIGNUP) {
	$this->title = '创建帐号并绑定第三方帐号';
	$btnLabel = '创建帐号并绑定';
} else {
	$this->title = '注册';
	$btnLabel = '注册';
}
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<div class="box">
		<div class="inner">
			<?= Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title ?>
		</div>
		<div class="cell cell-form">
            <?php $form = ActiveForm::begin([
			    'layout' => 'horizontal',
				'id' => 'form-signup'
			]); ?>
<?php if ($model->action === SignupForm::ACTION_AUTH_SIGNUP) : ?>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-6" style="padding-top:7px;">
					您已通过<?= $authInfo['sourceName'] ?>登录
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">用户名</label>
				<div class="col-sm-6" style="padding-top:7px;">
					<strong><?= $authInfo['username'] ?></strong>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">已有本站帐号</label>
				<div class="col-sm-6">
					<?= Html::a('绑定已有帐号', ['auth-bind-account'], ['class'=>'btn btn-primary']) ?>
				</div>
			</div>
			<br /><strong>创建本站账号</strong><hr>
<?php endif; ?>
                <?= $form->field($model, 'username')->textInput(['maxlength'=>20]) ?>
                <?= $form->field($model, 'email')->textInput(['maxlength'=>50]) ?>
                <?= $form->field($model, 'password')->passwordInput(['maxlength'=>20]) ?>
                <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength'=>20]) ?>
<?php
	if ( $model->action !== SignupForm::ACTION_AUTH_SIGNUP && intval(Yii::$app->params['settings']['captcha_enabled']) === 1 ) {
		echo $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname());
	}
?>
                <div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
                    <?= Html::submitButton($btnLabel, ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
					</div>
                </div>
            <?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?= $this->render('@app/views/common/_right') ?>
</div>
<!-- sf-right end -->

</div>
