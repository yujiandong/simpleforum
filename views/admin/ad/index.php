<?php
/**
 * @link http://www.simpleforum.org/
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

<div class="box">
	<div class="inner">
		<p class='fr'><?php echo Html::a('创建新广告', ['add']); ?></p>
		<?php
			echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
		?>
	</div>
	<div class="cell bg-info"><strong>广告</strong>
	</div>
	<div class="cell">
		<ul>
		<?php
			foreach($ads as $ad) {
				echo '<li>[', Ad::LOCATIONS[$ad['location']], ']&nbsp;', $ad['name'], '&nbsp;|&nbsp;', 
					Html::a('修改', ['edit', 'id'=>$ad['id']]), '&nbsp;|&nbsp;', 
					Html::a('删除', ['delete', 'id'=>$ad['id']], [
					    'data' => [
					        'confirm' => '注意：删除后将不会恢复！确认删除！',
					        'method' => 'post',
					]]), '</li>';
			}
		?>
		</ul>
	</div>
	<div class="item-pagination">
	<?php
	echo LinkPager::widget([
	    'pagination' => $pages,
		'maxButtonCount'=>5,
	]);
	?>
	</div>
</div>


</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?= $this->render('@app/views/common/_admin-right') ?>
</div>
<!-- sf-right end -->
</div>
