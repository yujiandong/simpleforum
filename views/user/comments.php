<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = Html::encode($user['username']).'的全部回复';
$settings = Yii::$app->params['settings'];

?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="box">
	<div class="cell">
		<?= Html::a('首页', ['topic/index']), '&nbsp;›&nbsp;', 
			Html::a(Html::encode($user['username']), ['user/view', 'username'=>$user['username']]), 
			'&nbsp;›&nbsp;全部回帖' ?>
	</div>
<?php
foreach($comments as $comment):
?>
	<div class="cell gray small bg-info">
		<p class='fr'><?= Yii::$app->getFormatter()->asRelativeTime($comment['created_at']) ?></p>
		回复了 <?=Html::encode($comment['topic']['author']['username']) ?> 创建的主题 › <?=Html::a(Html::encode($comment['topic']['title']), ['topic/view', 'id'=>$comment['topic_id']]) ?>
	</div>
	<div class="cell">
		<?= Html::encode($comment['content']) ?>
	</div>
<?php endforeach; ?>
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

<div class="col-md-4 sf-right">
<?= $this->render('@app/views/common/_right') ?>
</div>

</div>
