<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Node;

$this->title = '导航节点设定';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-heading">
		<?php
			echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', Html::a('导航管理', ['index']), '&nbsp;/&nbsp;', $this->title;
		?>
	</div>
	<div class="panel-body">
<?php $form = ActiveForm::begin([
			'id' => 'form-navi'
		]); ?>
	<table class="table table-bordered">
	<thead>
		<tr><th>节点</th><?php echo ($node['type']==1)?'<th>显示</th>':''; ?><th>排序</th><th>删除</th></tr>
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
			<?php echo Html::submitButton('确定', ['class' => 'btn btn-primary']); ?> <?php echo Html::button('添加', ['class' => 'btn btn-primary navi-nodes-add']); ?>
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
