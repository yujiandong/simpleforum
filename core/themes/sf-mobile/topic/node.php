<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Topic;
use app\models\Favorite;
use app\components\SfHtml;

$settings = Yii::$app->params['settings'];
$currentPage = $pages->page+1;

if ( !($isGuest = Yii::$app->getUser()->getIsGuest()) ) {
    $me = Yii::$app->getUser()->getIdentity();
}

if (!$isGuest && $me->isActive()) {
    $follow = Favorite::checkFollow($me->id, Favorite::TYPE_NODE, $node['id'])?Html::a('<i class="fas fa-star fa-lg aria-hidden="true""></i><span class="favorite-num">' . ($node['favorite_count']>0?$node['favorite_count']:'') . '</span>', null, ['class'=>'favorite', 'title'=>Yii::t('app', 'Cancle Favorite'), 'href' => '#', 'onclick'=> 'return false;', 'params'=>'unfavorite node '. $node['id']]):Html::a('<i class="far fa-star fa-lg" aria-hidden="true"></i><span class="favorite-num">' . ($node['favorite_count']>0?$node['favorite_count']:'') . '</span>', null, ['class'=>'favorite', 'title'=>Yii::t('app', 'Favorite'), 'href' => '#', 'onclick' => 'return false;', 'params'=>'favorite node '. $node['id']]);
    $follow = '  •  '.$follow;
} else {
    $follow = '';
}

$this->title = Html::encode($node['name']);
?>

<div class="row">

<!-- sf-left start -->
<div class="col-lg-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item sf-box-header">
        <span class="fr gray small"><?php echo Yii::t('app', '{n, plural, =0{no topics} =1{# topic} other{# topics}}', ['n'=>intval($node['topic_count'])]), $follow; ?></span>
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
        <p class="gray"><?php echo Html::encode($node['about']); ?></p>
        <?php
            if (!$isGuest && $me->isActive() ) {
                echo Html::a('<i class="fas fa-pencil-alt"></i>'.Yii::t('app', 'Add Topic'), ['topic/add', 'node'=>$node['ename']], ['class'=>'btn sf-btn']);
            }
        ?>
    </li>
    <?php
    foreach($topics as $topic){
        $topic = $topic['topic'];
        $url = ['topic/view', 'id'=>$topic['id']];
//      if ( $currentPage > 1) {
            $url['np'] = $currentPage;
//      }
        echo '<li class="list-group-item media">',
                SfHtml::uImgLink($topic['author']),
                '<div class="media-body">
                    <div class="small gray mt-0">';
                    echo '<strong><i class="fa fa-user" aria-hidden="true"></i>', SfHtml::uLink($topic['author']['username'], $topic['author']['name']), SfHtml::uGroupRank($topic['author']['score']), '</strong>',
                    $topic['top']==1?' •  <i class="fa fa-arrow-up" aria-hidden="true"></i>'.Yii::t('app', 'Top'):'',
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
                    echo '<i class="far fa-clock" aria-hidden="true"></i>'.Yii::$app->formatter->asRelativeTime($topic['replied_at']);
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

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
