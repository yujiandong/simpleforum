<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = '修改标签';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-heading">
		<?php
			echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', Html::a('标签管理', ['index']), '&nbsp;/&nbsp;' . $this->title;
		?>
	</div>
	<div class="panel-body">
<?php $form = ActiveForm::begin();
	echo $form->field($model, 'name')->textInput(['maxlength' => 20]);
?>
		<div class="form-group">
			<?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?>
		</div>
<?php ActiveForm::end(); ?>
	</div>
</div>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->
</div>
