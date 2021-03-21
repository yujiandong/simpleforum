<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Node;
use app\models\Navi;
use app\components\SfHtml;

$settings = Yii::$app->params['settings'];
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
<div class="col-lg-8 sf-left">

<ul class="list-group sf-box">
<?php
if(!Yii::$app->getUser()->getIsGuest()):
    $me = Yii::$app->getUser()->getIdentity();
?>
    <li class="list-group-item sf-box-header">
<?php
    echo '<span class="fr">' . Html::a('<i class="fas fa-pencil-alt"></i>'.Yii::t('app', 'Add Topic'), ['topic/new']) . '</span>';
    echo Html::a(SfHtml::uScore($me->score), ['my/balance'], ['class'=>'btn btn-sm btn-light sf-box-info']);
    echo ($me->getNoticeCount()>0?' '.Html::a('<i class="fas fa-bell"></i>(+'.$me->getNoticeCount().')', ['my/notifications']):'');
    if( $me->checkTodaySigned() === false ) {
        echo ' ', Html::a('<i class="fa fa-gift" aria-hidden="true"></i>' . Yii::t('app', 'Daily Bonus'), ['service/signin'], ['class'=>'btn btn-sm btn-light sf-box-info']);
    }
?>
    </li>
<?php endif; ?>
<?php
    $navis = Navi::getHeadNaviNodes();
    if ($navis) {
        echo '<li class="list-group-item navi-top-list' . (Yii::$app->getUser()->getIsGuest()?' sf-box-header':'') . '">';
        echo Html::a(Yii::t('app', 'All Topics'), ['topic/index'], ['class'=>'btn btn-sm sf-btn current']);
        foreach($navis as $current) {
            echo Html::a(Html::encode($current['name']), ['topic/navi', 'name'=>$current['ename']]);
        }
        echo '</li>';
    }
?>
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
                    <div class="small gray mt-0">';
                   echo '<strong><i class="fa fa-user"></i>', SfHtml::uLink($topic['author']['username'], $topic['author']['name']), SfHtml::uGroupRank($topic['author']['score']), '</strong>',
                    $topic['alltop']==1?' • <i class="fa fa-arrow-up"></i>'.Yii::t('app', 'Top'):'',
                    '</div>
                    <h5>';
         if($topic['comment_count'] > 0){
            $gotopage = ceil($topic['comment_count']/intval($settings['comment_pagesize']));
            if($gotopage > 1){
                $url['p'] = $gotopage;
            }
            echo Html::a($topic['comment_count'], $url, ['class'=>'badge fr count-info']);
        }
                    echo Html::a(Html::encode($topic['title']), $url), $topic['comment_closed']==1?' <i class="fa fa-lock gray" aria-hidden="true"></i>':'',
                    '</h5>
                    <div class="small gray">';
                    echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'btn btn-sm btn-light small']), ' • <i class="far fa-clock"></i>'.Yii::$app->formatter->asRelativeTime($topic['replied_at']);
        if ($topic['comment_count']>0) {
                    echo '<span class="item-lastreply"> • <i class="fa fa-comment" aria-hidden="true"></i>', SfHtml::uLink($topic['lastReply']['username']), '</span>';
        }
                    echo '</div>
                </div>';
        echo '</li>';
    }
    ?>
    <li class="list-group-item sf-pagination">
    <?php
    echo LinkPager::widget([
        'pagination' => $pages,
        'maxButtonCount'=>5,
        'listOptions' => ['class'=>'pagination justify-content-center my-2'],
        'activeLinkCssClass' => ['sf-btn'],
    ]);
    ?>
    </li>
</ul>

<?php
if ( intval($settings['cache_enabled'])===0 || $this->beginCache('f-bottom-nodes', ['duration' => intval($settings['cache_time'])*60])) :
?>
<ul class="list-group sf-box bottom-navi">
    <li class="list-group-item sf-box-header"><span class="fr"><?php echo Html::a(Yii::t('app', 'All Nodes'), ['node/index']); ?></span><?php echo Yii::t('app', 'Node Navi'); ?>
    </li>
<?php
    $bNavis = Navi::getBottomNaviNodes();
    foreach($bNavis as $cNavi) :
?>
    <li class="list-group-item vertical-align">
        <div class="col-4 col-sm-3 col-lg-2 gray text-right"><?php echo Html::encode($cNavi['name']); ?></div>
        <div class="col-8 col-sm-9 col-lg-10 navi-links">
        <?php
            foreach($cNavi['naviNodes'] as $cNode) {
                $cNode = $cNode['node'];
                echo Html::a(Html::encode($cNode['name']), ['topic/node', 'name'=>$cNode['ename']]);
            }
        ?>
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
<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_index-right'); ?>
</div>
<!-- sf-right end -->

</div>
