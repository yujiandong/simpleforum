<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Navi;

$this->title = Yii::t('app/admin', 'Navigations');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<p class='fr'><?php echo Html::a(Yii::t('app/admin', 'Add a item'), ['add']); ?></p>
		<?php
			echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
		?>
	</li>
	<li class="list-group-item list-group-item-info"><strong><?php echo Yii::t('app/admin', 'Navigation Items'); ?></strong></li>
	<li class="list-group-item">
		<ul>
		<?php
			$naviTypes = Navi::getTypes();
			foreach($models as $model) {
				echo '<li> [', $naviTypes[$model['type']], '] ', $model['name'], '&nbsp;|&nbsp;', 
					Html::a(Yii::t('app/admin', 'Set Nodes'), ['nodes', 'id'=>$model['id']]), '&nbsp;|&nbsp;', 
					Html::a(Yii::t('app', 'Edit'), ['edit', 'id'=>$model['id']]), '&nbsp;|&nbsp;', 
					Html::a(Yii::t('app', 'Delete'), ['delete', 'id'=>$model['id']], [
					    'data' => [
					        'confirm' => Yii::t('app', 'Are you sure you want to delete it? This operation cannot be undone.'),
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
