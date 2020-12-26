<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

\app\assets\Select2Asset::register($this);
$this->registerJs("$('.select2').select2();");

$this->title = Yii::t('app/admin', 'Move Node');

$request = Yii::$app->getRequest();
$me = Yii::$app->getUser()->getIdentity();

$indexUrl = ['topic/index'];
$nodeUrl = ['topic/node', 'name'=>$model['node']['ename']];
if ($request->get('ip', 1) > 1) {
	$indexUrl['p'] = $request->get('ip', 1);
}
if ($request->get('np', 1) > 1) {
	$nodeUrl['p'] = $request->get('np', 1);
}
?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-heading">
		<?php echo Html::a(Yii::t('app', 'Home'), $indexUrl), '&nbsp;/&nbsp;', Html::a(Html::encode($model['node']['name']), $nodeUrl), '&nbsp;/&nbsp;', $this->title; ?>
	</div>
	<div class="panel-body">
<?php $form = ActiveForm::begin(); ?>
    <?php
		 echo $form->field($model, 'node_id')->dropDownList(\app\models\Node::getNodeList(), ['class'=>'form-control select2']);
	?>
    <?php echo $form->field($model, 'title')->textArea(['rows' => '4', 'maxlength'=>120, 'readonly'=>'readonly']); ?>
	<div class="form-group">
		<?php echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Edut'), ['class' => 'btn btn-primary']); ?>
	</div>
<?php ActiveForm::end(); ?>
	</div>
</div>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
