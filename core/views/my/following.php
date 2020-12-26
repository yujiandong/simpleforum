<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use app\components\SfHtml;

$settings = Yii::$app->params['settings'];
$formatter = Yii::$app->getFormatter();

$this->title = Yii::t('app', 'Topics of my following');;
?>

<div class="row">
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<?php
			echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title;
		?>
	</li>
<?php
foreach($topics as $topic){
	$topic = $topic['topic'];

	echo '<li class="list-group-item media">',
		SfHtml::uImgLink($topic['author']),
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
				'  •  <strong><i class="fa fa-user" aria-hidden="true"></i>', SfHtml::uLink($topic['author']['username'], $topic['author']['name']), '</strong>',
				' • <i class="fa fa-clock-o" aria-hidden="true"></i>', $formatter->asRelativeTime($topic['replied_at']);
	if ($topic['comment_count']>0) {
				echo '<span class="item-lastreply"> • <i class="fa fa-reply" aria-hidden="true"></i>', SfHtml::uLink($topic['lastReply']['username']), '</span>';
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
