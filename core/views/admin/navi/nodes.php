<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Node;

$this->title = Yii::t('app/admin', 'Set Nodes');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-lg-8 sf-left">

<div class="card sf-box">
	<div class="card-header sf-box-header sf-navi">
		<?php
			echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', Html::a(Yii::t('app/admin', 'Navigations'), ['index']), '&nbsp;/&nbsp;', $this->title;
		?>
	</div>
	<div class="card-body">
<?php $form = ActiveForm::begin([
			'id' => 'form-navi'
		]); ?>
	<table class="table table-bordered">
	<thead>
		<tr><th><?php echo Yii::t('app', 'Node'); ?></th><?php echo ($node['type']==1)?'<th>'.Yii::t('app/admin', 'Show').'</th>':''; ?><th><?php echo Yii::t('app/admin', 'Sort ID'); ?></th><th><?php echo Yii::t('app', 'Delete'); ?></th></tr>
	</thead>
	<tbody class="navi-nodes">
<?php
	$key = 0;
	foreach($models as $model) :
	$key++;
?>
		<tr id="node_<?php echo $key; ?>">
<?php
	echo '<td>', 
			$form->field($model, '['.$key.']node_id')->dropDownList(array(''=>'')+Node::getNodeList(), ['class'=>'form-control nodes-select2 node-id'])->label(false),
			'</td>';
	echo ($node['type']==1)?'<td>'.$form->field($model, '['.$key.']visible')->checkbox(['class'=>'visible', 'label' => null]).'</td>':'';
	echo '<td>', 
			$form->field($model, '['.$key.']sortid')->textInput(['maxlength' => 2, 'class'=>'form-control sortid'])->label(false),
			'</td><td><span class="navi-nodes-del">X</span></td>';
?>
		</tr>
<?php
	endforeach;
?>
	</tbody>
	</table>
	<div class="form-group">
		<?php echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn sf-btn']); ?> <?php echo Html::button(Yii::t('app', 'Add'), ['class' => 'btn sf-btn navi-nodes-add']); ?>
	</div>
<?php ActiveForm::end(); ?>
	</div>
</div>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->
</div>
