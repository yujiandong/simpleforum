<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

$this->title = '邮件发送测试';

?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-heading">
		<?php echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;›&nbsp;', Html::a('配置管理', ['admin/setting']), '&nbsp;›&nbsp;', $this->title; ?>
	</div>
	<div class="panel-body sf-box-form">
<?php
if ( $rtnCd === 9 ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-warning'],
		   'body' => '测试邮件发送出错，请确认 '. Html::a('SMTP邮箱设置', ['setting/update', '#'=>'mailer']) . ' 是否正确。',
		]);
} else if ( $rtnCd === 1 ) {
	echo Alert::widget([
		   'options' => ['class' => 'alert-success'],
		   'body' => '测试邮件发送成功，请进测试邮箱查看是否收到。',
		]);
}
?>
<?php $form = ActiveForm::begin([
		'action' => ['admin/setting/test-email'],
		'layout' => 'horizontal',
		]); ?>
		<?php echo $form->field($model, 'email')->textInput(['maxlength'=>50]); ?>
		<?php echo $form->field($model, 'content')->textArea(['maxlength'=>255]); ?>
        <div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('测试', ['class' => 'btn btn-primary']); ?>
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
