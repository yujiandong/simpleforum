<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = Html::encode($user['username']).'的全部主题';
$settings = Yii::$app->params['settings'];

?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="box">
	<div class="inner">
		<?php echo Html::a('首页', ['topic/index']), '&nbsp;›&nbsp;', 
			Html::a(Html::encode($user['username']), ['user/view', 'username'=>$user['username']]), 
			'&nbsp;›&nbsp;全部主题'; ?>
	</div>
<?php
foreach($topics as $topic){
	$topic = $topic['topic'];
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
				' •  ', Yii::$app->getFormatter()->asRelativeTime($topic['replied_at']);
	if ($topic['comment_count']>0) {
				echo '<span class="item-lastreply"> •  最后回复者 ', Html::a(Html::encode($topic['lastReply']['username']), ['user/view', 'username'=>Html::encode($topic['lastReply']['username'])]), '</span>';
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

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
