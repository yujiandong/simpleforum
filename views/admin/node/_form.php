<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Node */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($model, 'ename')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($model, 'about')->textarea(['rows' => 3, 'maxlength' => 255]) ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? '创建' : '修改', ['class' => 'btn btn-primary']) ?>
	</div>

<?php ActiveForm::end(); ?>