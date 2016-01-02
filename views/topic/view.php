<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\lib\Util;
use app\models\User;
use app\models\Favorite;

$settings = Yii::$app->params['settings'];
$request = Yii::$app->getRequest();
$formatter = Yii::$app->getFormatter();

$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);
$editor->registerAsset($this);

$whiteWrapClass = $settings['editor']=='smd'?'white-wrap':'';

$indexPage = intval($request->get('ip', 0));
$nodePage = intval($request->get('np', 0));

$indexUrl = ['topic/index'];
$nodeUrl = ['topic/node', 'name'=>$topic['node']['ename']];
$topicUrl = ['topic/edit', 'id'=>$topic['id']];
if ($indexPage > 0) {
	if ($indexPage > 1) {
		$indexUrl['p'] = $indexPage;
	}
	$topicUrl['ip'] = $indexPage;
} else if ($nodePage > 0) {
	if ($nodePage > 1) {
		$nodeUrl['p'] = $nodePage;
	}
	$topicUrl['np'] = $nodePage;
}

$isGuest = Yii::$app->getUser()->getIsGuest();
$topicOp = [];
if(!$isGuest) {
	$me = Yii::$app->getUser()->getIdentity();
	if ($me->isActive()) {
		$topicOp['follow'] = Favorite::checkFollow($me->id, Favorite::TYPE_TOPIC, $topic['id'])?Html::a('取消收藏', ['favorite/cancel', 'type'=>'topic', 'id'=>$topic['id']], [
		    'data' => [
		        'method' => 'post',
		    ]]):Html::a('加入收藏', ['favorite/add', 'type'=>'topic', 'id'=>$topic['id']]);
	}
	if ( $me->canReply($topic) ) {
		$topicOp['reply'] = Html::a('回复', null, ['href' => '#reply']);
	}
	if ( $me->canEdit($topic) ) {
		$topicOp['edit'] = Html::a('修改', $topicUrl);
	}
	if ($me->isAdmin()) {
		$topicUrl[0] = 'admin/topic/change-node';
		$topicOp['changeNode'] = Html::a('转移节点', $topicUrl);
		$topicUrl[0] = 'admin/topic/delete';
		$topicOp['delete'] = Html::a('删除', $topicUrl, [
		    'data' => [
		        'confirm' => '注意：删除后将不会恢复！确认删除！',
		        'method' => 'post',
		    ]]);
	}
}

$this->title = Html::encode($topic['title']);
?>
<div class="row">
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-heading">
		<div class="fr">
			<?php echo Html::a(Html::img('@web/'.str_replace('{size}', 'large', $topic['author']['avatar']), ['class'=>'img-rounded media-object','alt'=> Html::encode($topic['author']['username'])]), ['user/view', 'username'=>Html::encode($topic['author']['username'])]); ?>
		</div>
		<?php
			echo Html::a('首页', $indexUrl), '&nbsp;/&nbsp;', Html::a(Html::encode($topic['node']['name']), $nodeUrl);
		?>
		<h2 class="word-wrap"><?php echo $this->title; ?></h2>
		<small class="gray">
		<?php
			echo 'by ', Html::a(Html::encode($topic['author']['username']), ['user/view', 'username'=>Html::encode($topic['author']['username'])]), '  •  ', $formatter->asRelativeTime($topic['created_at']), '  •  ', $topic['views'], ' 次点击';
			echo '  •  字体 <span class="fontsize-plus">大</span> <span class="fontsize-minus">小</span>';
			if ( !$isGuest && !empty($topicOp) ) {
				echo '  •  ', implode(' | ', $topicOp);
			}
		?></small>
	</div>
<?php if(!empty($topic['content']['content']) || !empty($topic['tags'])) : ?>
	<div class="panel-body content link-external word-wrap <?php echo $whiteWrapClass; ?>">
		<?php
		if(!empty($topic['content']['content'])) {
			if ( $topic['invisible'] == 1 || $topic['author']['status'] == User::STATUS_BANNED ) {
				echo '<p class="bg-danger">此主题已被屏蔽</p>';
				if (!$isGuest && $me->isAdmin()) {
					echo $editor->parse($topic['content']['content']);
				}
			} else {
				echo $editor->parse($topic['content']['content']);
			}
		}
		if( !empty($topic['tags']) ) {
			echo '<div class="top10">';
			$tags = explode(',', strtolower($topic['tags']));
			foreach($tags as $tag) {
				echo Html::a(Html::encode($tag), ['tag/index', 'name'=>$tag], ['class'=>'btn btn-default btn-sm tag']);
			}
			echo '</div>';
		}
		?>
	</div>
<?php endif; ?>
	<div class="panel-footer bdsharebuttonbox"></div>
</div>

<?php if( intval($topic['comment_count']) > 0 ) : ?>
<ul class="list-group sf-box">
	<li class="list-group-item">
<?php echo $topic['comment_count'], '&nbsp;回复&nbsp;|&nbsp;直到&nbsp;', 
	$formatter->asDateTime($topic['replied_at'], 'y-MM-dd HH:mm:ss xxx') ?>
	</li>
<?php
foreach($comments as $comment){
//	$comment = $comment['comment'];
	echo '<li class="list-group-item media" id="reply', $comment['position'] ,'">
			<div class="media-left item-avatar">',
				Html::img('@web/'.str_replace('{size}', 'normal', $comment['author']['avatar']), ['class'=>'img-rounded media-object','alt'=> Html::encode($comment['author']['username'])], ['class'=>'media-left item-avatar']),
			'</div>
		 	 <div class="media-body link-external">
				<div><small class="fr gray">';
	if ( !$isGuest ) {
		$commentUrl = ['comment/edit', 'id'=>$comment['id']];
/*		if ($indexPage > 0) {
			$commentUrl['ip'] = $indexPage;
		}
		if ($nodePage > 0) {
			$commentUrl['np'] = $nodePage;
		}*/
		if ( $me->canEdit($comment, $topic['comment_closed']) ) {
			echo Html::a('修改', $commentUrl), ' | ';
		}
		if ( $me->isAdmin() ) {
			$commentUrl[0] = 'admin/comment/delete';
			echo Html::a('删除', $commentUrl, [
			    'data' => [
			        'confirm' => '注意：删除后将不会恢复！确认删除！',
			        'method' => 'post',
			    ]]), ' | ';
		}
		if ( $me->canReply($topic) ) {
			echo Html::a('回复', null, ['href' => 'javascript:replyTo("'. Html::encode($comment['author']['username']) .'");']), ' | ';
		}
	}
					echo $comment['position'].'楼',
				'</small>
				<small class="gray"><strong>', Html::a(Html::encode($comment['author']['username']), ['user/view', 'username'=>Html::encode($comment['author']['username'])]), '</strong> •  ',
				 $formatter->asRelativeTime($comment['created_at']),
				'</small></div>';
				if ( $comment['invisible'] == 1 || $comment['author']['status'] == User::STATUS_BANNED ) {
					echo Alert::widget([
						'options' => ['class' => 'alert-warning'],
						'closeButton'=>false,
						'body' => '此回复已被屏蔽',
					]);
					if (!$isGuest && $me->isAdmin()) {
						echo '<div class="comment-content word-wrap ',$whiteWrapClass,'">', $editor->parse($comment['content']) , '</div>';
					}
				} else {
					echo '<div class="comment-content word-wrap ',$whiteWrapClass,'">', $editor->parse($comment['content']) , '</div>';
				}
			echo '</div>';
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
<?php endif; ?>

<?php if( !$isGuest && $me->canReply($topic) ): ?>
<div class="panel panel-default sf-box" id="reply">
	<div class="panel-heading">
		<span class="fr"><a href="#">↑ 回到顶部</a></span>添加一条新回复
	</div>
	<div class="panel-body">
<?php $form = ActiveForm::begin(['action' => ['comment/reply', 'id'=>$topic['id']]]);
	echo $form->field(new \app\models\Comment(), 'content')->textArea(['id'=>'editor'])->label(false);
	if($me->canUpload($settings)) {
		$editor->registerUploadAsset($this);
		echo '<div class="form-group"><div id="fileuploader">图片上传</div></div>';
	}
?>
    <div class="form-group">
        <?php echo Html::submitButton('回复', ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end(); ?>
	</div>
</div>
<?php endif; ?>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
