<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin(); ?>

<?php
	echo $form->field($model, 'name')->textInput(['maxlength' => 20]);
	echo $form->field($model, 'url')->textInput(['maxlength' => 100]);
	echo $form->field($model, 'sortid')->textInput(['maxlength' => 2])->hint(Yii::t('app/admin', 'The results will be sorted from smallest to largest. Default value is {n}.', ['n'=>99]));
?>
	<div class="form-group">
		<?php echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Edit'), ['class' => 'btn btn-primary']); ?>
	</div>
<?php ActiveForm::end(); ?>
