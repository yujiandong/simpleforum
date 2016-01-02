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

$this->title = '我关注的人的最新主题';
?>

<div class="row">
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<?php
			echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title;
		?>
	</li>
<?php
foreach($topics as $topic){
	$topic = $topic['topic'];

	echo '<li class="list-group-item media">',
				Html::a(Html::img('@web/'.str_replace('{size}', 'normal', $topic['author']['avatar']), ['class'=>'img-rounded media-object','alt' => Html::encode($topic['author']['username'])]), ['user/view', 'username'=>Html::encode($topic['author']['username'])], ['class'=>'media-left item-avatar']),
			'<div class="media-body">
				<h5 class="media-heading">',
				Html::a(Html::encode($topic['title']), ['topic/view', 'id'=>$topic['id']]),
				'</h5>
				<div class="small gray">';
	if($topic['comment_count'] > 0){
	    $gotopage = ceil($topic['comment_count']/$settings['comment_pagesize']);
		$url = ['topic/view', 'id'=>$topic['id']];
	    if($gotopage >= 1){
			$url['p'] = $gotopage;
	    }
		echo Html::a($topic['comment_count'], $url, ['class'=>'badge fr count-info']);
	}
				echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'btn btn-xs node small']),
				'  •  <strong>', Html::a(Html::encode($topic['author']['username']),['user/view', 'username'=>Html::encode($topic['author']['username'])]), '</strong>',
				' •  ', Yii::$app->formatter->asRelativeTime($topic['replied_at']);
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

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
