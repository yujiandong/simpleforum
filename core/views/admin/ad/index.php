<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Ad;

$this->title = '广告管理';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<p class='fr'><?php echo Html::a('创建新广告', ['add']); ?></p>
		<?php
			echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
		?>
	</li>
	<li class="list-group-item list-group-item-info"><strong>导航</strong></li>
	<li class="list-group-item">
		<ul>
		<?php
			$locations = json_decode(Ad::LOCATIONS,true);
			foreach($ads as $ad) {
				echo '<li>[', $locations[$ad['location']], ']&nbsp;', $ad['name'], '&nbsp;|&nbsp;', 
					Html::a('修改', ['edit', 'id'=>$ad['id']]), '&nbsp;|&nbsp;', 
					Html::a('删除', ['delete', 'id'=>$ad['id']], [
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
