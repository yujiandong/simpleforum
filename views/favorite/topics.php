<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\i18n\Formatter;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;

$settings = Yii::$app->params['settings'];

$this->title = '我收藏的主题';
?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="box">
	<div class="cell">
		<?php
			echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title;
		?>
	</div>
<?php
foreach($topics as $topic){
	$topic = $topic['topic'];

	echo '<div class="cell item clearfix">
			<div class="item-avatar">',
				Html::a(Html::img('@web/'.str_replace('{size}', 'normal', $topic['author']['avatar']), ["alt" => Html::encode($topic['author']['username'])]), ['user/view', 'username'=>Html::encode($topic['author']['username'])]),
			'</div>
		 	 <div class="item-content">
				<div class="item-title">',
				Html::a(Html::encode($topic['title']), ['topic/view', 'id'=>$topic['id']]),
				'</div>
				<div class="small">';
	if($topic['comment_count'] > 0){
	    $gotopage = ceil($topic['comment_count']/intval($settings['comment_pagesize']));
	    if($gotopage == 1){
			$url = ['topic/view', 'id'=>$topic['id']];
	    }else{
			$url = ['topic/view', 'id'=>$topic['id'], 'p'=>$gotopage];
	    }
		echo '<div class="item-commentcount">', Html::a($topic['comment_count'], $url, ['class'=>'count_livid']),'</div>';
	}
				echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'node']),
				'  •  <strong>', Html::a(Html::encode($topic['author']['username']),['user/view', 'username'=>Html::encode($topic['author']['username'])]), '</strong>',
				' •  ', Yii::$app->formatter->asRelativeTime($topic['replied_at']);
	if ($topic['comment_count']>0) {
				echo '<span class="item-lastreply"> •  最后回复者 ', Html::a(Html::encode($topic['lastReply']['username']), ['user/view', 'username'=>Html::encode($topic['lastReply']['username'])]), '</span>';
	}
				echo '</div>
			</div>';


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
<?= $this->render('@app/views/common/_right') ?>
</div>
