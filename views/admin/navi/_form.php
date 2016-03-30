<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Navi;

?>

<?php $form = ActiveForm::begin([
		    'layout' => 'horizontal',
			'id' => 'form-navi'
		]);

$naviTypes = json_decode(Navi::TYPES,true);

if($type === 'edit') {
?>
		<div class="form-group">
			<label class="control-label col-sm-3 username-label">类型</label>
			<div class="col-sm-6" style="padding-top:7px;">
				<strong><?php echo $naviTypes[$model->type]; ?></strong>
			</div>
		</div>

<?php
} else {
	echo $form->field($model, 'type')->dropDownList($naviTypes);
}
	echo $form->field($model, 'name')->textInput(['maxlength' => 20]);
	echo $form->field($model, 'ename')->textInput(['maxlength' => 20]);
	echo $form->field($model, 'sortid')->textInput(['maxlength' => 2])->hint('数字越小越靠前');
?>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<?php echo Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => 'btn btn-primary']); ?>
		</div>
	</div>
<?php ActiveForm::end(); ?>
