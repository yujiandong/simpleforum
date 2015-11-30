<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\Alert;
use app\models\Favorite;
use app\models\User;

$this->title = Html::encode($user['username']);
$settings = Yii::$app->params['settings'];
$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);

$fomatter = Yii::$app->getFormatter();
$isGuest = Yii::$app->getUser()->getIsGuest();
if (!$isGuest) {
	$me = Yii::$app->getUser()->getIdentity();
}

if (!$isGuest && $me->isActive() && $me->id != $user['id']) {
	$follow = Favorite::checkFollow($me->id, Favorite::TYPE_USER, $user['id'])?Html::a('取消特别关注', ['favorite/cancel', 'type'=>'user', 'id'=>$user['id']], [
		'class'=>'btn btn-sm btn-default',
	    'data' => [
	        'method' => 'post',
	    ]]):Html::a('加入特别关注', ['favorite/add', 'type'=>'user', 'id'=>$user['id']], ['class'=>'btn btn-sm btn-primary']);
} else {
	$follow = '';
}

if (!$isGuest && $me->isAdmin() && $me->id != $user['id']) {
	$manage = Html::a('管理', ['admin/user/info', 'id'=>$user['id']], ['class'=>'btn btn-sm btn-primary']);
} else {
	$manage = '';
}

?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="box">
	<div class="cell clearfix">
		<span class="fr sf-btn"><?php echo $manage, $follow; ?></span>
		<div class="item-largeavatar">
			<?php echo Html::img('@web/'.str_replace('{size}', 'large', $user['avatar']), ["alt" => $this->title]); ?>
		</div>
		<div class="item-userinfo">
			<h1><?php echo $this->title; ?></h1>
			<p class="gray"><?php echo $settings['site_name'],' 第 ',$user['id'],' 号会员，加入于 ',$fomatter->asDateTime($user['created_at'], 'y-MM-dd HH:mm:ss xxx'); ?>
			</p>
		</div>
	</div>
	<?php if( !empty($user['userInfo']['about']) || !empty($user['userInfo']['website']) ) : ?>
	<div class="cell link-external">
		<?php echo empty($user['userInfo']['about'])?'':'<p>'.Html::encode($user['userInfo']['about']).'</p>'; ?>
		<?php echo empty($user['userInfo']['website'])?'':'个人网站： '.Html::a($user['userInfo']['website'], $user['userInfo']['website'], ['target'=>'_blank', 'rel' => 'external']); ?>
	</div>
	<?php endif ?>
</div>

<div class="box">
	<div class="inner gray">
		<?php echo Html::encode($user['username']); ?> 最近创建的主题
	</div>
<?php
foreach($user['topics'] as $topic){
	echo '<div class="cell item clearfix">
				<div class="item-title">',
				Html::a(Html::encode($topic['title']), ['topic/view', 'id'=>$topic['id']]),
				'</div>
				<div class="small">';
	if($topic['comment_count'] > 0){
	    $gotopage = ceil($topic['comment_count']/$settings['comment_pagesize']);
	    if($gotopage == 1){
			$url = ['topic/view', 'id'=>$topic['id']];
	    }else{
			$url = ['topic/view', 'id'=>$topic['id'], 'p'=>$gotopage];
	    }
		echo '<div class="item-commentcount">', Html::a($topic['comment_count'], $url, ['class'=>'count_livid']),'</div>';
	}
				echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'node']),
				' •  ', $fomatter->asRelativeTime($topic['replied_at']);
	if ($topic['comment_count']>0) {
				echo '<span class="item-lastreply"> •  最后回复者 ', Html::a(Html::encode($topic['lastReply']['username']), ['user/view', 'username'=>Html::encode($topic['lastReply']['username'])]), '</span>';
	}
				echo '</div>';


	echo '</div>';
}
?>
	<div class="cell">
		» <?php echo Html::a(Html::encode($user['username']).'创建的更多主题', ['topics', 'username'=>Html::encode($user['username'])]); ?>
	</div>
</div>

<div class="box">
	<div class="inner gray">
		<?php echo Html::encode($user['username']); ?> 最近回复了
	</div>
<?php
foreach($user['comments'] as $comment) :
?>
	<div class="cell gray small bg-info">
		<p class='fr'><?php echo $fomatter->asRelativeTime($comment['created_at']); ?></p>
		回复了 <?php echo Html::encode($comment['topic']['author']['username']); ?> 创建的主题 › <?php echo Html::a(Html::encode($comment['topic']['title']), ['topic/view', 'id'=>$comment['topic_id']]); ?>
	</div>
	<div class="cell comment-content white-space">
	<?php
		if ( $comment['invisible'] == 1 || $user['status'] == User::STATUS_BANNED ) {
			echo Alert::widget([
				'options' => ['class' => 'alert-warning'],
				'closeButton'=>false,
				'body' => '此回复已被屏蔽',
			]);
			if (!$isGuest && $me->isAdmin()) {
				echo $editor->parse($comment['content']);
			}
		} else {
			echo $editor->parse($comment['content']);
		}
	?>
	</div>
<?php endforeach; ?>
	<div class="cell">
		» <?php echo Html::a(Html::encode($user['username']).'的更多回复', ['comments', 'username'=>Html::encode($user['username'])]); ?>
	</div>
</div>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
