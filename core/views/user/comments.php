<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\bootstrap\Alert;
use app\models\User;

$this->title = Html::encode($user['username']).'的全部回复';
$settings = Yii::$app->params['settings'];
$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);

$whiteWrapClass = $settings['editor']=='smd'?'white-wrap':'';

?>

<div class="row">
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<?php echo Html::a('首页', ['topic/index']), '&nbsp;›&nbsp;', 
			Html::a(Html::encode($user['username']), ['user/view', 'username'=>$user['username']]), 
			'&nbsp;›&nbsp;全部回帖'; ?>
	</li>
<?php
foreach($comments as $comment):
?>
	<li class="list-group-item gray small list-group-item-info">
		<p class='fr'><?php echo Yii::$app->getFormatter()->asRelativeTime($comment['created_at']); ?></p>
		回复了 <?php echo Html::encode($comment['topic']['author']['username']); ?> 创建的主题 › <?php echo Html::a(Html::encode($comment['topic']['title']), ['topic/view', 'id'=>$comment['topic_id']]); ?>
	</li>
	<li class="list-group-item word-wrap <?php echo $whiteWrapClass; ?>">
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
	</li>
<?php endforeach; ?>
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

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
