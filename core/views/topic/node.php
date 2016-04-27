<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Topic;
use app\models\User;
use app\models\Favorite;

$settings = Yii::$app->params['settings'];
$currentPage = $pages->page+1;
if (!Yii::$app->getUser()->getIsGuest() && Yii::$app->getUser()->getIdentity()->isActive()) {
	$follow = Favorite::checkFollow(Yii::$app->getUser()->id, Favorite::TYPE_NODE, $node['id'])?Html::a('取消收藏', ['favorite/cancel', 'type'=>'node', 'id'=>$node['id']], [
	    'data' => [
	        'method' => 'post',
	    ]]):Html::a('加入收藏', ['favorite/add', 'type'=>'node', 'id'=>$node['id']]);
	$follow = '  •  '.$follow;
} else {
	$follow = '';
}

$this->title = Html::encode($node['name']);
?>

<div class="row">

<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<span class="fr gray small">主题总数 <?php echo $node['topic_count'], $follow; ?></span>
		<?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
		<p class="gray"><?php echo Html::encode($node['about']); ?></p>
		<?php
			if (!Yii::$app->getUser()->getIsGuest() && Yii::$app->getUser()->getIdentity()->status >= User::STATUS_ACTIVE ) {
				echo Html::a('<i class="fa fa-pencil"></i>发表新主题', ['topic/add', 'node'=>$node['ename']], ['class'=>'btn btn-primary']);
			}
		?>
	</li>
	<?php
	foreach($topics as $topic){
		$topic = $topic['topic'];
		$url = ['topic/view', 'id'=>$topic['id']];
//		if ( $currentPage > 1) {
			$url['np'] = $currentPage;
//		}
		echo '<li class="list-group-item media">',
				Html::a(Html::img('@web/'.str_replace('{size}', 'normal', $topic['author']['avatar']), ['class'=>'img-circle media-object','alt'=>Html::encode($topic['author']['username'])]), ['user/view', 'username'=>Html::encode($topic['author']['username'])], ['class'=>'media-left item-avatar']),
				'<div class="media-body">
					<h5 class="media-heading">',
					Html::a(Html::encode($topic['title']), $url),
					'</h5>
					<div class="small gray">';
		if($topic['comment_count'] > 0){
		    $gotopage = ceil($topic['comment_count']/$settings['comment_pagesize']);
		    if($gotopage > 1){
				$url['p'] = $gotopage;
		    }
			echo Html::a($topic['comment_count'], $url, ['class'=>'badge fr count-info']);
		}
					echo '<strong><i class="fa fa-user"></i>', Html::a(Html::encode($topic['author']['username']),['user/view', 'username'=>Html::encode($topic['author']['username'])]), '</strong>',
					' •  ', $topic['top']==1?'<i class="fa fa-arrow-up"></i>置顶':'<i class="fa fa-clock-o"></i>'.Yii::$app->formatter->asRelativeTime($topic['replied_at']);
		if ($topic['comment_count']>0) {
					echo '<span class="item-lastreply"> • <i class="fa fa-reply"></i>', Html::a(Html::encode($topic['lastReply']['username']), ['user/view', 'username'=>Html::encode($topic['lastReply']['username'])]), '</span>';
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

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
