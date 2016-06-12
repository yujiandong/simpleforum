<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Node;
use app\models\Navi;
use app\lib\Util;

$settings = Yii::$app->params['settings'];
$currentPage = $pages->page+1;

if( empty($title) ) {
    $this->title = Html::encode($settings['site_name']);
    $title = '最近更新';
} else {
    $this->title = Html::encode($title);
}
if($currentPage > 1) {
    $this->title = $this->title . ' - 第' . $currentPage . '页';
}
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 col-sm-12 sf-left">

<ul class="list-group sf-box">
<?php if(!Yii::$app->getUser()->getIsGuest()): 
    $me = Yii::$app->getUser()->getIdentity();
?>
    <li class="list-group-item">
<?php
    echo '<span class="fr">' . Html::a('<i class="fa fa-pencil"></i>发表', ['topic/new']) . '</span>';
    echo Html::a('<i class="fa fa-bell'.($me->getNoticeCount()>0?'':'-o').'"></i>'.$me->getNoticeCount().' 条提醒', ['my/notifications']);
    echo ' ', Html::a(Util::getScore($me->score), ['my/balance'], ['class'=>'btn btn-xs node']);
?>
    </li>
<?php endif; ?>
    <li class="list-group-item navi-top-list">
<?php
    echo Html::a('全部', ['topic/index'], ['class'=>'btn btn-sm btn-primary current']);
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
                Html::a(Html::img('@web/'.str_replace('{size}', 'normal', $topic['author']['avatar']), ['class'=>'img-circle media-object','alt' => Html::encode($topic['author']['username'])]), ['user/view', 'username'=>Html::encode($topic['author']['username'])], ['class'=>'media-left item-avatar']),
                '<div class="media-body">
                    <div class="small gray">';
                   echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'btn btn-xs node small']),
                    ' • <strong><i class="fa fa-user"></i>', Html::a(Html::encode($topic['author']['username']),['user/view', 'username'=>Html::encode($topic['author']['username'])]), Util::getGroupRank($topic['author']['score']), '</strong>',
                    $topic['alltop']==1?' • <i class="fa fa-arrow-up"></i>置顶':'',
                    '</div>
					<h5 class="media-heading">';
         if($topic['comment_count'] > 0){
            $gotopage = ceil($topic['comment_count']/intval($settings['comment_pagesize']));
            if($gotopage > 1){
                $url['p'] = $gotopage;
            }
            echo Html::a($topic['comment_count'], $url, ['class'=>'badge fr count-info']);
        }
                    echo Html::a(Html::encode($topic['title']), $url),
                    '</h5>
                    <div class="small gray">';
                    echo '<i class="fa fa-clock-o"></i>'.Yii::$app->formatter->asRelativeTime($topic['replied_at']);
        if ($topic['comment_count']>0) {
                    echo '<span> • <i class="fa fa-reply"></i>', Html::a(Html::encode($topic['lastReply']['username']), ['user/view', 'username'=>Html::encode($topic['lastReply']['username'])]), '</span>';
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
    <li class="list-group-item gray"><span class="fr"><?php echo Html::a('浏览全部节点', ['node/index']); ?></span>节点导航
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
