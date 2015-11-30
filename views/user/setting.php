<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\models\UserInfo;
use app\models\UploadForm;
use app\models\ChangePasswordForm;
use app\models\ChangeEmailForm;

$this->title = '会员设置';
$session = Yii::$app->getSession();
$me = Yii::$app->getUser()->getIdentity();
$cpModel = new ChangePasswordForm();
$ceModel = new ChangeEmailForm();

?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="box">
	<div class="inner">
		<?php echo Html::a('首页', ['topic/index']), '&nbsp;›&nbsp;', $this->title; ?>
	</div>
<?php if ($me->isWatingActivation()) : ?>
	<div class="cell bg-info" id="activate"><strong>会员激活</strong></div>
	<div class="cell">
<?php
if ( $session->hasFlash('activateMailNG') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-warning'],
		   'body' => $session->getFlash('activateMailNG'),
		]);
} else if ( $session->hasFlash('activateMailOK') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-success'],
		   'body' => $session->getFlash('activateMailOK'),
		]);
}
?>
		您还有没有激活，请进您注册时填写的邮箱(<?php echo $me->email; ?>)，点击激活链接。<br />
		<?php echo Html::a('重发激活邮件', ['user/send-activate-mail']); ?> | <a href="#password">修改邮箱</a>
	</div>
<?php endif; ?>
	<div class="cell bg-info" id="info"><strong>修改信息</strong></div>
	<div class="cell cell-form">
<?php
if ( $session->hasFlash('EditProfileNG') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-warning'],
		   'body' => $session->getFlash('EditProfileNG'),
		]);
} else if ( $session->hasFlash('EditProfileOK') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-success'],
		   'body' => $session->getFlash('EditProfileOK'),
		]);
}
?>
<?php $form = ActiveForm::begin([
		'action' => ['user/edit-profile'],
		'layout' => 'horizontal',
		]); ?>
		<div class="form-group">
			<label class="control-label col-sm-3 username-label">用户名</label>
			<div class="col-sm-6" style="padding-top:7px;">
				<strong><?php echo $me->username; ?></strong>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3 status-label">状态</label>
			<div class="col-sm-6" style="padding-top:7px;">
				<?php echo $me->getStatus(); ?>
			</div>
		</div>
		<?php echo $form->field($me->userInfo, 'website')->textInput(['maxlength'=>100]); ?>
		<?php echo $form->field($me->userInfo, 'about')->textArea(['maxlength'=>255]); ?>
        <div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?>
			</div>
        </div>
<?php ActiveForm::end(); ?>
	</div>
	<div class="cell bg-info" id="avatar"><strong>修改头像</strong></div>
	<div class="cell cell-form">
<?php
if ( $session->hasFlash('setAvatarNG') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-warning'],
		   'body' => $session->getFlash('setAvatarNG'),
		]);
} else if ( $session->hasFlash('setAvatarOK') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-success'],
		   'body' => $session->getFlash('setAvatarOK'),
		]);
}
?>
<?php $form = ActiveForm::begin([
		'action' => ['user/avatar'],
		'options' => ['enctype' => 'multipart/form-data'],
		'layout' => 'horizontal',
		]); ?>
		<div class="form-group">
			<label class="control-label col-sm-3 avatar-label">当前头像</label>
			<div class="col-sm-6">
			<?php echo Html::img('@web/'.str_replace('{size}', 'large', $me->avatar)), ' ', Html::img('@web/'.str_replace('{size}', 'normal', $me->avatar)), ' ', Html::img('@web/'.str_replace('{size}', 'small', $me->avatar)); ?>
			</div>
		</div>
		<?php echo $form->field((new UploadForm()), 'file')->fileInput(); ?>

        <div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('上传', ['class' => 'btn btn-primary']); ?>
			</div>
        </div>
<?php ActiveForm::end(); ?>
	</div>
	<div class="cell bg-info" id="email"><strong>修改邮箱</strong></div>
	<div class="cell cell-form">
<?php
if ( $session->hasFlash('chgEmailNG') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-warning'],
		   'body' => $session->getFlash('chgEmailNG'),
		]);
} else if ( $session->hasFlash('chgEmailOK') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-success'],
		   'body' => $session->getFlash('chgEmailOK'),
		]);
}
?>
<?php $form = ActiveForm::begin([
		'action' => ['user/change-email'],
		'layout' => 'horizontal',
		]);
?>
		<div class="form-group">
			<label class="control-label col-sm-3 username-label">当前邮箱</label>
			<div class="col-sm-6" style="padding-top:7px;">
				<strong><?php echo $me->email; ?></strong>
			</div>
		</div>
		<?php echo $form->field($ceModel, 'email')->textInput(['maxlength'=>50]); ?>
		<?php echo $form->field($ceModel, 'password')->passwordInput(['maxlength'=>20]); ?>
        <div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?>
			</div>
        </div>
<?php ActiveForm::end(); ?>
	</div>
	<div class="cell bg-info" id="password"><strong>修改密码</strong></div>
	<div class="cell cell-form">
<?php
if ( $session->hasFlash('chgPwdNG') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-warning'],
		   'body' => $session->getFlash('chgPwdNG'),
		]);
} else if ( $session->hasFlash('chgPwdOK') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-success'],
		   'body' => $session->getFlash('chgPwdOK'),
		]);
}
?>
<?php $form = ActiveForm::begin([
		'action' => ['user/change-password'],
		'layout' => 'horizontal',
		]);
?>
		<?php echo $form->field($cpModel, 'old_password')->passwordInput(['maxlength'=>20]); ?>
		<?php echo $form->field($cpModel, 'password')->passwordInput(['maxlength'=>20]); ?>
		<?php echo $form->field($cpModel, 'password_repeat')->passwordInput(['maxlength'=>20]); ?>
        <div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?>
			</div>
        </div>
<?php ActiveForm::end(); ?>
	</div>
</div>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
