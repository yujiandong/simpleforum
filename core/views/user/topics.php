<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\components\SfHtml;

$this->title = Yii::t('app', '{username}\'s All Topics', ['username'=>$user['username']]);
$settings = Yii::$app->params['settings'];
$formatter = Yii::$app->getFormatter();

?>

<div class="row">
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;›&nbsp;', SfHtml::uLink($user['username'], $user['name']), '&nbsp;›&nbsp;', Yii::t('app', 'All Topics'); ?>
	</li>
<?php
foreach($topics as $topic){
	$topic = $topic['topic'];
	echo '<li class="list-group-item">
				<h5 class="media-heading">',
				Html::a(Html::encode($topic['title']), ['topic/view', 'id'=>$topic['id']]), $topic['comment_closed']==1?' <i class="fa fa-lock gray" aria-hidden="true"></i>':'',
				'</h5>
				<div class="small gray">';
	if($topic['comment_count'] > 0){
	    $gotopage = ceil($topic['comment_count']/$settings['comment_pagesize']);
	    if($gotopage == 1){
			$url = ['topic/view', 'id'=>$topic['id']];
	    }else{
			$url = ['topic/view', 'id'=>$topic['id'], 'p'=>$gotopage];
	    }
		echo Html::a($topic['comment_count'], $url, ['class'=>'badge fr count-info']);
	}
				echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'btn btn-xs node small']),
				' •  ', $formatter->asRelativeTime($topic['replied_at']);
	if ($topic['comment_count']>0) {
				echo '<span class="item-lastreply"> •  ' , Yii::t('app', 'by {username}', ['username'=>SfHtml::uLink($topic['lastReply']['username'])]) , '</span>';
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

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
