<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = Yii::t('app/admin', 'Tags');
$formatter = Yii::$app->getFormatter();
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<?php
			echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
		?>
	</li>
	<li class="list-group-item list-group-item-info"><strong>标签</strong></li>
	<li class="list-group-item">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th><?php echo Yii::t('app', 'Tag'); ?></th>
          <th><?php echo Yii::t('app/admin', 'Create time'); ?></th>
          <th><?php echo Yii::t('app/admin', 'Operation'); ?></th>
        </tr>
      </thead>
      <tbody>
<?php
    foreach($tags as $tag) {
        echo '<tr><td>', $tag['id'], '</td>
			<td>', Html::a(Html::encode($tag['name']), ['tag/index', 'name'=>$tag['name']], ['target'=>'_blank']), '</td>
			<td>', $formatter->asDateTime($tag['created_at'], 'y-MM-dd HH:mmZ') ,'</td>
			<td>', Html::a(Yii::t('app', 'Edit'), ['edit', 'id'=>$tag['id']]).'&nbsp;|&nbsp;', 
	            Html::a(Yii::t('app', 'Delete'), ['delete', 'id'=>$tag['id']], [
	                'data' => [
	                    'confirm' => Yii::t('app', 'Are you sure you want to delete it? This operation cannot be undone.'),
	                    'method' => 'post',
	            ]]), '</td>
		</tr>';
    }
?>
      </tbody>
    </table>
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
