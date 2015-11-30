<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$settings = Yii::$app->params['settings'];
$this->title = '全部节点';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 col-sm-12 sf-left">

<?php
if ( intval($settings['cache_enabled']) ===0 || $this->beginCache('f-all-nodes', ['duration' => intval($settings['cache_time'])*60])) :
?>
<div class="box">
	<div class="inner">
	<?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
	</div>
	<div class="inner hot-nodes sf-btn">
<?php
	$nodes = \app\models\Node::getAllNodes();
	foreach($nodes as $node) {
		echo Html::a(Html::encode($node['name']), ['topic/node', 'name'=>$node['ename']], ['class'=>'btn btn-default']);
	}
?>
	</div>
</div>
<?php
if ( intval($settings['cache_enabled']) !== 0 ) {
	$this->endCache();
}
endif;
?>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
