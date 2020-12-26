<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Topic */

$this->title = Yii::t('app/admin', 'Member\'s information');
$session = Yii::$app->getSession();
$formatter = Yii::$app->getFormatter();
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<?php echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title; ?>
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
			<label class="control-label col-sm-3"><?php echo Yii::t('app', 'Username'); ?></label>
			<div class="col-sm-9">
				<p class="form-control-static"><strong><?php echo $user->username; ?></strong></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3"><?php echo Yii::t('app/admin', 'Register time'); ?></label>
			<div class="col-sm-9">
				<p class="form-control-static"><strong><?php echo $formatter->asDateTime($user->getUser()->created_at, 'y-MM-dd HH:mm:ssZ'); ?></strong></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3"><?php echo Yii::t('app/admin', 'Register IP'); ?></label>
			<div class="col-sm-9">
				<p class="form-control-static"><strong><?php echo long2ip($user->getUser()->userInfo->reg_ip); ?></strong></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3"><?php echo Yii::t('app/admin', 'Last login time'); ?></label>
			<div class="col-sm-9">
				<p class="form-control-static"><strong><?php echo $formatter->asDateTime($user->getUser()->userInfo->last_login_at, 'y-MM-dd HH:mm:ssZ'); ?></strong></p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3"><?php echo Yii::t('app/admin', 'Last login IP'); ?></label>
			<div class="col-sm-9">
				<p class="form-control-static"><strong><?php echo long2ip($user->getUser()->userInfo->last_login_ip); ?></strong></p>
			</div>
		</div>
	</li>
	<li class="list-group-item list-group-item-info"><strong><?php echo Yii::t('app', 'Edit {attribute}', ['attribute'=>Yii::t('app/admin', 'Member\'s information')]); ?></strong></li>
	<li class="list-group-item sf-box-form">
<?php
if ( $session->hasFlash('adminProfileNG') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-warning'],
		   'body' => Yii::t('app', $session->getFlash('adminProfileNG')),
		]);
} else if ( $session->hasFlash('adminProfileOK') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-success'],
		   'body' => Yii::t('app', $session->getFlash('adminProfileOK')),
		]);
}
?>
		<?php echo $form->field($user, "status")->dropDownList(User::getStatusList()); ?>
		<?php echo $form->field($user, "email")->textInput(['maxlength'=>50]); ?>
        <div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app', 'Edit'), ['class' => 'btn btn-primary']); ?>
			</div>
        </div>
	</li>
<?php
	ActiveForm::end();
?>
	<li class="list-group-item list-group-item-info"><strong><?php echo Yii::t('app', 'Edit {attribute}', ['attribute'=>Yii::t('app', 'Password')]); ?></strong></li>
	<li class="list-group-item sf-box-form">
<?php
if ( $session->hasFlash('adminPwdNG') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-warning'],
		   'body' => Yii::t('app', $session->getFlash('adminPwdNG')),
		]);
} else if ( $session->hasFlash('adminPwdOK') ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-success'],
		   'body' => Yii::t('app', $session->getFlash('adminPwdOK')),
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
        	<?php echo Html::submitButton(Yii::t('app', 'Edit'), ['class' => 'btn btn-primary']); ?>
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
