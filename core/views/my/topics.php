<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\components\SfHtml;

$settings = Yii::$app->params['settings'];
$formatter = Yii::$app->getFormatter();
$this->title = Yii::t('app', 'My favorite topics');
?>

<div class="row">
<div class="col-lg-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item sf-box-header sf-navi">
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
                Html::a(Html::encode($topic['title']), ['topic/view', 'id'=>$topic['id']]), $topic['comment_closed']==1?' <i class="fa fa-lock gray" aria-hidden="true"></i>':'',
                '</h5>
                <div class="small gray">';
    if($topic['comment_count'] > 0){
        $gotopage = ceil($topic['comment_count']/intval($settings['comment_pagesize']));
        $url = ['topic/view', 'id'=>$topic['id']];
        if($gotopage >= 1){
            $url['p'] = $gotopage;
        }
        echo Html::a($topic['comment_count'], $url, ['class'=>'badge fr count-info']);
    }
                echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'btn btn-sm btn-light small']),
                '  •  <strong><i class="fa fa-user"></i>', SfHtml::uLink($topic['author']['username'], $topic['author']['name']), '</strong>',
                ' • <i class="far fa-clock"></i>', $formatter->asRelativeTime($topic['replied_at']);
    if ($topic['comment_count']>0) {
                echo '<span class="item-lastreply"> • <i class="fa fa-reply"></i>', SfHtml::uLink($topic['lastReply']['username']), '</span>';
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

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
