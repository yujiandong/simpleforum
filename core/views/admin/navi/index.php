<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Navi;

$this->title = '导航管理';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<p class='fr'><?php echo Html::a('添加新导航', ['add']); ?></p>
		<?php
			echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
		?>
	</li>
	<li class="list-group-item list-group-item-info"><strong>导航</strong></li>
	<li class="list-group-item">
		<ul>
		<?php
			$naviTypes = json_decode(Navi::TYPES,true);
			foreach($models as $model) {
				echo '<li> [', $naviTypes[$model['type']], '] ', $model['name'], '&nbsp;|&nbsp;', 
					Html::a('所属节点设定', ['nodes', 'id'=>$model['id']]), '&nbsp;|&nbsp;', 
					Html::a('修改', ['edit', 'id'=>$model['id']]), '&nbsp;|&nbsp;', 
					Html::a('删除', ['delete', 'id'=>$model['id']], [
					    'data' => [
					        'confirm' => '注意：删除后将不会恢复！确认删除！',
					        'method' => 'post',
					]]), '</li>';
			}
		?>
		</ul>
	</li>
	<li class="list-group-item item-pagination">
	<?php
	echo LinkPager::widget([
	    'pagination' => $pages,
		'maxButtonCount'=>5,
	]);
	?>
	</li>
</ul>


</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->
</div>
