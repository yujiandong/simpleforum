<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

?>

<?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => 'form-link',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-form-label col-sm-3 text-sm-right',
                    'wrapper' => 'col-sm-9',
                ],
            ],
       ]); ?>

<?php
	echo $form->field($model, 'name')->textInput(['maxlength' => 20]);
	echo $form->field($model, 'url')->textInput(['maxlength' => 100]);
	echo $form->field($model, 'sortid')->textInput(['maxlength' => 2])->hint(Yii::t('app/admin', 'The results will be sorted from smallest to largest. Default value is {n}.', ['n'=>99]));
?>
	<div class="form-group offset-sm-3 col-sm-9">
		<?php echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Edit'), ['class' => 'btn sf-btn']); ?>
	</div>
<?php ActiveForm::end(); ?>
