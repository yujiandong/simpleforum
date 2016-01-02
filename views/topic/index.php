<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Node;

$settings = Yii::$app->params['settings'];
$currentPage = $pages->page+1;

if( empty($title) ) {
	$this->title = Html::encode($settings['site_name']);
	$title = '最近更新';
} else {
	$this->title = Html::encode($title);
}
if($currentPage > 1) {
	$this->title = $this->title . ' - 第' . $currentPage . '页';
}
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 col-sm-12 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
	<?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', Html::encode($title); ?>
	</li>
	<?php
	foreach($topics as $topic){
		$topic = $topic['topic'];
		$url = ['topic/view', 'id'=>$topic['id']];
//		if ( $currentPage > 1) {
			$url['ip'] = $currentPage;
//		}
		echo '<li class="list-group-item media">',
				Html::a(Html::img('@web/'.str_replace('{size}', 'normal', $topic['author']['avatar']), ['class'=>'img-rounded media-object','alt' => Html::encode($topic['author']['username'])]), ['user/view', 'username'=>Html::encode($topic['author']['username'])], ['class'=>'media-left item-avatar']),
				'<div class="media-body">
					<h5 class="media-heading">',
					Html::a(Html::encode($topic['title']), $url),
					'</h5>
					<div class="small gray">';
		if($topic['comment_count'] > 0){
		    $gotopage = ceil($topic['comment_count']/intval($settings['comment_pagesize']));
		    if($gotopage > 1){
				$url['p'] = $gotopage;
		    }
			echo Html::a($topic['comment_count'], $url, ['class'=>'badge fr count-info']);
		}
					echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'btn btn-xs node small']),
					'  •  <strong>', Html::a(Html::encode($topic['author']['username']),['user/view', 'username'=>Html::encode($topic['author']['username'])]), '</strong>',
					' •  ', $topic['alltop']==1?'置顶':Yii::$app->formatter->asRelativeTime($topic['replied_at']);
		if ($topic['comment_count']>0) {
					echo '<span class="item-lastreply"> •  最后回复者 ', Html::a(Html::encode($topic['lastReply']['username']), ['user/view', 'username'=>Html::encode($topic['lastReply']['username'])]), '</span>';
		}
					echo '</div>
				</div>';

		echo '</li>';
	}
	?>
	<li class="list-group-item item-pagination">
	<?php
	echo LinkPager::widget([
	    'pagination' => $pages,
		'maxButtonCount'=>5,
	]);
	?>
	</li>

</ul>

<?php
if ( intval($settings['cache_enabled'])===0 || $this->beginCache('f-hot-nodes', ['duration' => intval($settings['cache_time'])*60])) :
?>
<div class="panel panel-default sf-box">
	<div class="panel-heading gray"><span class="fr"><?php echo Html::a('浏览全部节点', ['node/index']); ?></span>热门节点</div>
	<div class="panel-body hot-nodes sf-btn">
<?php
	$hotNodes = Node::getHotNodes();
	foreach($hotNodes as $hn) {
		echo Html::a(Html::encode($hn['name']), ['topic/node', 'name'=>$hn['ename']], ['class'=>'btn btn-default']);
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
<?php echo $this->render('@app/views/common/_index-right'); ?>
</div>
<!-- sf-right end -->

</div>
