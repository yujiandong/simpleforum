<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Ad;

?>

<?php $form = ActiveForm::begin(); ?>

<?php
    echo $form->field($model, 'name')->textInput(['maxlength' => 20]);
    echo $form->field($model, 'location')->dropDownList(json_decode(Ad::LOCATIONS, true));
    echo $form->field($model, 'node_id')->textInput(['maxlength' => 10]);
    echo $form->field($model, 'expires')->textInput(['maxlength' => 10])->hint('格式：2016-12-12');
    echo $form->field($model, 'sortid')->textInput(['maxlength' => 2])->hint('数字越小越靠前，默认50');
    echo $form->field($model, 'content')->textArea(['rows' => '6'])->hint('支持HTML语法');
?>
	<div class="form-group">
		<?php echo Html::submitButton($model->isNewRecord ? '创建' : '修改', ['class' => 'btn btn-primary']); ?>
	</div>
<?php ActiveForm::end(); ?>
