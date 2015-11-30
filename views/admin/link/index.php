<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '链接管理';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="box">
	<div class="inner">
		<p class='fr'><?php echo Html::a('创建新链接', ['add']); ?></p>
		<?php
			echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
		?>
	</div>
	<div class="cell bg-info"><strong>链接</strong>
	</div>
	<div class="cell">
		<ul>
		<?php
			foreach($links as $link) {
				echo '<li>', $link['name'], '&nbsp;(', Html::a($link['url'], $link['url']), ')&nbsp;|&nbsp;', 
					Html::a('修改', ['edit', 'id'=>$link['id']]), '&nbsp;|&nbsp;', 
					Html::a('删除', ['delete', 'id'=>$link['id']], [
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
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->
</div>
