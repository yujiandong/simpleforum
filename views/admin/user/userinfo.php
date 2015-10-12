<?php
/**
 * @link http://www.simpleforum.org/
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

?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="box">
	<div class="inner">
		<?= Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title; ?>
	</div>
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
	'id' => 'form-setting',
    'fieldConfig' => [
//			        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-2',
//			            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-10',
            'error' => '',
            'hint' => 'col-sm-offset-2 col-sm-10',
        ],
    ],
]); ?>
	<div class="cell">
		<div class="form-group">
			<label class="control-label col-sm-2">用户名</label>
			<div class="col-sm-10" style="padding-top:7px;">
				<strong><?= $user->username ?></strong>
			</div>
		</div>
	</div>
	<div class="cell bg-info"><strong>修改信息</strong>
	</div>
	<div class="cell">
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
		<?= $form->field($user, "status")->dropDownList(User::$statusOptions) ?>
		<?= $form->field($user, "email")->textInput(['maxlength'=>50]) ?>
        <div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton('修改', ['class' => 'btn btn-primary']) ?>
			</div>
        </div>
	</div>
<?php
	ActiveForm::end();
?>
	<div class="cell bg-info"><strong>修改密码</strong>
	</div>
	<div class="cell">
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
	            'label' => 'col-sm-2',
//			            'offset' => 'col-sm-offset-4',
	            'wrapper' => 'col-sm-10',
	            'error' => '',
	            'hint' => 'col-sm-offset-2 col-sm-10',
	        ],
	    ],
	]); ?>
		<?= $form->field($user, "password")->textInput(['maxlength'=>20]) ?>
        <div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton('修改', ['class' => 'btn btn-primary']) ?>
			</div>
        </div>
	<?php
		ActiveForm::end();
	?>
	</div>
</div>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?= $this->render('@app/views/common/_admin-right') ?>
</div>
<!-- sf-right end -->

</div>
