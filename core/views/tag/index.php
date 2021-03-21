<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Topic;
use app\models\User;
use app\models\Favorite;
use app\components\SfHtml;

$settings = Yii::$app->params['settings'];
$formatter = Yii::$app->getFormatter();
$currentPage = $pages->page+1;

$this->title = Html::encode($tag['name']);
?>

<div class="row">

<!-- sf-left start -->
<div class="col-lg-8 sf-left">


<ul class="list-group sf-box">
    <li class="list-group-item sf-box-header sf-navi">
        <span class="fr small"><?php echo Yii::t('app', '{n, plural, =0{no topics} =1{# topic} other{# topics}}', ['n'=>intval($tag['topic_count'])]); ?></span>
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </li>
    <?php
    foreach($topics as $topic){
        $topic = $topic['topic'];
        if( empty($topic) ) {
            continue;
        }
        $url = ['topic/view', 'id'=>$topic['id']];
//        if ( $currentPage > 1) {
            $url['ip'] = $currentPage;
//        }
        echo '<li class="list-group-item media">',
                SfHtml::uImgLink($topic['author']),
                '<div class="media-body">
                    <h5 class="media-heading">',
                    Html::a(Html::encode($topic['title']), $url),
                    '</h5>
                    <div class="small gray">';
        if($topic['comment_count'] > 0){
            $gotopage = ceil($topic['comment_count']/intval($settings['comment_pagesize']));
            if($gotopage > 1){
                $url['p'] = $gotopage;
            }
            echo '<div class="item-commentcount">', Html::a($topic['comment_count'], $url, ['class'=>'badge fr count-info']),'</div>';
        }
                    echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'btn btn-sm btn-light small']),
                    '  •  <strong><i class="fa fa-user" aria-hidden="true"></i>', SfHtml::uLink($topic['author']['username'], $topic['author']['name']), SfHtml::uGroupRank($topic['author']['score']), '</strong>',
                    ' • <i class="far fa-clock" aria-hidden="true"></i>', $formatter->asRelativeTime($topic['replied_at']);
        if ($topic['comment_count']>0) {
                    echo '<span class="item-lastreply"> • <i class="fa fa-reply" aria-hidden="true"></i>', SfHtml::uLink($topic['lastReply']['username']), '</span>';
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
<?= $this->render('@app/views/common/_right') ?>
</div>
<!-- sf-right end -->

</div>
