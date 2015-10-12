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
use yii\helpers\HtmlPurifier;
use app\lib\Util;
use app\models\User;
use app\models\Favorite;

$settings = Yii::$app->params['settings'];
$request = Yii::$app->getRequest();
$formatter = Yii::$app->getFormatter();

$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);
$editor->registerAsset($this);

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

<div class="box">
	<div class="cell topic-header">
		<div class="fr">
			<?= Html::a(Html::img('@web/'.str_replace('{size}', 'large', $topic['author']['avatar']), ["alt" => Html::encode($topic['author']['username'])]), ['user/view', 'username'=>Html::encode($topic['author']['username'])]) ?>
		</div>
		<?php
			echo Html::a('首页', $indexUrl), '&nbsp;/&nbsp;', Html::a(Html::encode($topic['node']['name']), $nodeUrl);
		?>
		<h1><?=$this->title ?></h1>
		<small class="gray">
		<?php
			echo 'by ', Html::a(Html::encode($topic['author']['username']), ['user/view', 'username'=>Html::encode($topic['author']['username'])]), '  •  ', $formatter->asRelativeTime($topic['created_at']), '  •  ', $topic['views'], ' 次点击';
			if ( !$isGuest && !empty($topicOp) ) {
				echo '  •  ', implode(' | ', $topicOp);
			}
		?></small>
	</div>
	<div class="cell topic-content link-external white-space">
		<?php
			if ( $topic['invisible'] == 1 || $topic['author']['status'] == User::STATUS_BANNED ) {
				echo '<p class="bg-danger">此主题已被屏蔽</p>';
				if (!$isGuest && $me->isAdmin()) {
					echo $editor->parse($topic['content']['content']);
				}
			} else {
				echo $editor->parse($topic['content']['content']);
			}
		?>
	</div>
</div>

<?php if( intval($topic['comment_count']) > 0 ) : ?>
<div class="box topic-comments">
	<div class="inner clearfix">
<?= $topic['comment_count'], '&nbsp;回复&nbsp;|&nbsp;直到&nbsp;', 
	$formatter->asDateTime($topic['replied_at'], 'y-MM-dd HH:mm:ss xxx') ?>
	</div>
<?php
foreach($comments as $comment){
//	$comment = $comment['comment'];
	echo '<div class="cell clearfix" id="reply', $comment['position'] ,'">
			<div class="item-avatar">',
				Html::a(Html::img('@web/'.str_replace('{size}', 'normal', $comment['author']['avatar']), ["alt" => Html::encode($comment['author']['username'])]), ['user/view', 'username'=>Html::encode($comment['author']['username'])]),
			'</div>
		 	 <div class="item-content link-external">
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
						echo '<div class="comment-content white-space">', $editor->parse($comment['content']) , '</div>';
					}
				} else {
					echo '<div class="comment-content white-space">', $editor->parse($comment['content']) , '</div>';
				}
			echo '</div>';
	echo '</div>';
}
?>
	<div class="item-pagination">
	<?php
	echo LinkPager::widget([
	    'pagination' => $pages,
		'maxButtonCount'=>5,
	]);
	?>
	</div>

</div>
<?php endif; ?>

<?php if( !$isGuest && $me->canReply($topic) ): ?>
<div class="box topic-comment" id="reply">
	<div class="inner">
		<span class="fr"><a href="#">↑ 回到顶部</a></span>添加一条新回复
	</div>
	<div class="cell">
<?php $form = ActiveForm::begin(['action' => ['comment/reply', 'id'=>$topic['id']]]); ?>
	<?= $form->field(new \app\models\Comment(), 'content')->textArea(['id'=>'editor'])->label(false) ?>
    <div class="form-group">
        <?= Html::submitButton('回复', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>
	</div>
</div>
<?php endif; ?>

</div>

<div class="col-md-4 sf-right">
<?= $this->render('@app/views/common/_right') ?>
</div>

</div>
