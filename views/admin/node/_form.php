<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin(); ?>

<?php
	echo $form->field($model, 'name')->textInput(['maxlength' => 50]);
    echo $form->field($model, 'ename')->textInput(['maxlength' => 50]);
    echo $form->field($model, 'about')->textarea(['rows' => 3, 'maxlength' => 255]);
 ?>
	<div class="form-group">
		<?php echo Html::submitButton($model->isNewRecord ? '创建' : '修改', ['class' => 'btn btn-primary']); ?>
	</div>
<?php ActiveForm::end(); ?>
