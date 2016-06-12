<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Topic;
use app\models\User;
use app\models\Favorite;

$settings = Yii::$app->params['settings'];
$currentPage = $pages->page+1;

$this->title = Html::encode($tag['name']);
?>

<div class="row">

<!-- sf-left start -->
<div class="col-md-8 sf-left">


<ul class="list-group sf-box">
	<li class="list-group-item">
		<span class="fr gray small">主题总数 <?= $tag['topic_count'] ?></span>
		<?= Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title ?>
	</li>
	<?php
	foreach($topics as $topic){
		$topic = $topic['topic'];
		if( empty($topic) ) {
			continue;
		}
		$url = ['topic/view', 'id'=>$topic['id']];
//		if ( $currentPage > 1) {
			$url['ip'] = $currentPage;
//		}
		echo '<li class="list-group-item media">',
				Html::a(Html::img('@web/'.str_replace('{size}', 'normal', $topic['author']['avatar']), ['class'=>'img-circle media-object','alt' => Html::encode($topic['author']['username'])]), ['user/view', 'username'=>Html::encode($topic['author']['username'])], ['class'=>'media-left item-avatar']),
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
			echo '<div class="item-commentcount">', Html::a($topic['comment_count'], $url, ['class'=>'badge fr count-info']),'</div>';
		}
					echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'btn btn-xs node small']),
					'  •  <strong><i class="fa fa-user"></i>', Html::a(Html::encode($topic['author']['username']),['user/view', 'username'=>Html::encode($topic['author']['username'])]), '</strong>',
					' • <i class="fa fa-clock-o"></i>', Yii::$app->formatter->asRelativeTime($topic['replied_at']);
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
<?= $this->render('@app/views/common/_right') ?>
</div>
<!-- sf-right end -->

</div>
