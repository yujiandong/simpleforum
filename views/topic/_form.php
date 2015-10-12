<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$editor = new \app\lib\Editor(['editor'=>Yii::$app->params['settings']['editor']]);
$editor->registerAsset($this);
?>

<?php $form = ActiveForm::begin(); ?>
    <?php
		if( $action === 'edit' && Yii::$app->getUser()->getIdentity()->isAdmin()) {
		 	echo $form->field($model, 'invisible')->dropDownList(['公开主题', '隐藏主题'])->label(false);
		 	echo $form->field($model, 'comment_closed')->dropDownList(['开放评论','关闭评论'])->label(false);
		}
	?>
	<p>主题标题 <span class="gray">( 如果标题能够表达完整内容，主题内容可为空 )</span></p>
    <?= $form->field($model, 'title')->textArea(['rows' => '4', 'maxlength'=>120])->label(false) ?>
	<p>主题内容</p>
	<?= $form->field($content, 'content')->textArea(['id'=>'editor', 'maxlength'=>30000])->label(false) ?>
	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? '创建' : '修改', ['class' => 'btn btn-primary']) ?>
	</div>
<?php ActiveForm::end(); ?>
