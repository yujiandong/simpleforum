<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Topic */

$this->title = '用户信息';
$session = Yii::$app->getSession();
$formatter = Yii::$app->getFormatter();
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<?php echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title; ?>
	</li>
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
	'id' => 'form-setting',
    'fieldConfig' => [
//			        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-3',
//			            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-9',
            'error' => '',
            'hint' => 'col-sm-offset-3 col-sm-9',
        ],
    ],
]); ?>
	<li class="list-group-item">
		<div class="form-group">
			<label class="control-label col-sm-3">用户名</label>
			<div class="col-sm-9">
				<p class="form-control-static"><strong><?php echo $user->username; ?></strong></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">注册时间</label>
			<div class="col-sm-9">
				<p class="form-control-static"><strong><?php echo $formatter->asDateTime($user->getUser()->created_at, 'y-MM-dd HH:mm:ss xxx'); ?></strong></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">注册IP</label>
			<div class="col-sm-9">
				<p class="form-control-static"><strong><?php echo long2ip($user->getUser()->userInfo->reg_ip); ?></strong></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">最后登录时间</label>
			<div class="col-sm-9">
				<p class="form-control-static"><strong><?php echo $formatter->asDateTime($user->getUser()->userInfo->last_login_at, 'y-MM-dd HH:mm:ss xxx'); ?></strong></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">最后登录IP</label>
			<div class="col-sm-9">
				<p class="form-control-static"><strong><?php echo long2ip($user->getUser()->userInfo->last_login_ip); ?></strong></p>
			</div>
		</div>
	</li>
	<li class="list-group-item list-group-item-info"><strong>修改信息</strong></li>
	<li class="list-group-item sf-box-form">
<?php
if ( $session->hasFlash('adminProfileNG') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-warning'],
		   'body' => $session->getFlash('adminProfileNG'),
		]);
} else if ( $session->hasFlash('adminProfileOK') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-success'],
		   'body' => $session->getFlash('adminProfileOK'),
		]);
}
?>
		<?php echo $form->field($user, "status")->dropDownList(User::$statusOptions); ?>
		<?php echo $form->field($user, "email")->textInput(['maxlength'=>50]); ?>
        <div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?>
			</div>
        </div>
	</li>
<?php
	ActiveForm::end();
?>
	<li class="list-group-item list-group-item-info"><strong>修改密码</strong></li>
	<li class="list-group-item sf-box-form">
<?php
if ( $session->hasFlash('adminPwdNG') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-warning'],
		   'body' => $session->getFlash('adminPwdNG'),
		]);
} else if ( $session->hasFlash('adminPwdOK') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-success'],
		   'body' => $session->getFlash('adminPwdOK'),
		]);
}
?>
    <?php $form = ActiveForm::begin([
		'action' => ['admin/user/reset-password', 'id'=>$user->id],
	    'layout' => 'horizontal',
		'id' => 'form-setting',
	    'fieldConfig' => [
//			        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
	        'horizontalCssClasses' => [
	            'label' => 'col-sm-3',
//			            'offset' => 'col-sm-offset-4',
	            'wrapper' => 'col-sm-9',
	            'error' => '',
	            'hint' => 'col-sm-offset-3 col-sm-9',
	        ],
	    ],
	]); ?>
		<?php echo $form->field($user, "password")->textInput(['maxlength'=>20]); ?>
        <div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?>
			</div>
        </div>
	<?php
		ActiveForm::end();
	?>
	</li>
</ul>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->

</div>
