<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Navi;
use app\components\SfHtml;

$settings = Yii::$app->params['settings'];
$formatter = Yii::$app->getFormatter();
$currentPage = $pages->page+1;

if( empty($title) ) {
    $this->title = Html::encode($settings['site_name']);
    $title = Yii::t('app', 'Latest');
} else {
    $this->title = Html::encode($title);
}
if($currentPage > 1) {
    $this->title = $this->title . ' - ' . Yii::t('app', 'Page {0,number}', $currentPage);
}
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 col-sm-12 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item navi-top-list">
<?php
    echo Yii::$app->getUser()->getIsGuest()?'':'<span class="fr">' . Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>'.Yii::t('app', 'Add Topic'), ['topic/new']) . '</span>';
    echo Html::a(Yii::t('app', 'All Topics'), ['topic/index'], ['class'=>'btn btn-sm btn-primary current']);
    $navis = Navi::getHeadNaviNodes();
    foreach($navis as $current) {
        echo Html::a(Html::encode($current['name']), ['topic/navi', 'name'=>$current['ename']]);
    }
?>
    </li>
    <?php
    foreach($topics as $topic){
        $topic = $topic['topic'];
        $url = ['topic/view', 'id'=>$topic['id']];
//      if ( $currentPage > 1) {
            $url['ip'] = $currentPage;
//      }
        echo '<li class="list-group-item media">',
                SfHtml::uImgLink($topic['author']),
                '<div class="media-body">
                    <h5 class="media-heading">',
                    Html::a(Html::encode($topic['title']), $url), $topic['comment_closed']==1?' <i class="fa fa-lock gray" aria-hidden="true"></i>':'',
                    '</h5>
                    <div class="small gray">';
        if($topic['comment_count'] > 0){
            $gotopage = ceil($topic['comment_count']/intval($settings['comment_pagesize']));
            if($gotopage > 1){
                $url['p'] = $gotopage;
            }
            echo Html::a($topic['comment_count'], $url, ['class'=>'badge fr count-info']);
        }
                    echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'btn btn-xs node small']),
                    ' • <strong><i class="fa fa-user" aria-hidden="true"></i>', SfHtml::uLink($topic['author']['username'], $topic['author']['name']), SfHtml::uGroupRank($topic['author']['score']), '</strong>',
                    ' • ', $topic['alltop']==1?'<i class="fa fa-arrow-up" aria-hidden="true"></i>'.Yii::t('app', 'Top'):'<i class="fa fa-clock-o" aria-hidden="true"></i>' . $formatter->asRelativeTime($topic['replied_at']);
        if ($topic['comment_count']>0) {
                    echo '<span class="item-lastreply"> • <i class="fa fa-comment" aria-hidden="true"></i>', SfHtml::uLink($topic['lastReply']['username']), '</span>';
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

<?php
if ( intval($settings['cache_enabled'])===0 || $this->beginCache('f-bottom-nodes', ['duration' => intval($settings['cache_time'])*60])) :
?>
<ul class="list-group sf-box bottom-navi">
	<li class="list-group-item gray"><span class="fr"><?php echo Html::a(Yii::t('app', 'All Nodes'), ['node/index']); ?></span><?php echo Yii::t('app', 'Node Navi'); ?>
	</li>
<?php
    $bNavis = Navi::getBottomNaviNodes();
    foreach($bNavis as $cNavi) :
?>
    <li class="list-group-item">
        <div class="row vertical-align">
        <div class="col-xs-3 col-sm-2 gray text-right"><?php echo Html::encode($cNavi['name']); ?></div>
        <div class="col-xs-9 col-sm-10 navi-links">
        <?php
            foreach($cNavi['naviNodes'] as $cNode) {
                $cNode = $cNode['node'];
                echo Html::a(Html::encode($cNode['name']), ['topic/node', 'name'=>$cNode['ename']]);
            }
        ?>
        </div>
        </div>
    </li>
<?php
    endforeach;
?>
</ul>
<?php
if ( intval($settings['cache_enabled']) !== 0 ) {
    $this->endCache();
}
endif;
?>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_index-right'); ?>
</div>
<!-- sf-right end -->

</div>
