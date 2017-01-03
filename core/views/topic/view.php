<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\components\SfHtml;
use app\models\User;
use app\models\Favorite;
use app\models\Topic;

$settings = Yii::$app->params['settings'];
$request = Yii::$app->getRequest();
$formatter = Yii::$app->getFormatter();

//$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);
$editorClass = '\app\plugins\\'. $settings['editor']. '\\'. $settings['editor'];
$editor = new $editorClass();
$editor->registerAsset($this);
\app\assets\LightboxAsset::register($this);

$whiteWrapClass = $settings['editor']=='SmdEditor'?'white-wrap':'';

$indexPage = intval($request->get('ip', 0));
$nodePage = intval($request->get('np', 0));

$indexUrl = ['topic/index'];
$nodeUrl = ['topic/node', 'name'=>$topic['node']['ename']];
$topicUrl = ['topic/edit', 'id'=>$topic['id']];
if ($indexPage > 0) {
    if ($indexPage > 1) {
        $indexUrl['p'] = $indexPage;
    }
    $topicUrl['ip'] = $indexPage;
} else if ($nodePage > 0) {
    if ($nodePage > 1) {
        $nodeUrl['p'] = $nodePage;
    }
    $topicUrl['np'] = $nodePage;
}

$isGuest = Yii::$app->getUser()->getIsGuest();
$topicOp = [];
if(!$isGuest) {
    $me = Yii::$app->getUser()->getIdentity();
    $topicOp['sms'] = Html::a('<i class="fa fa-envelope fa-lg" aria-hidden="true"></i>', ['service/sms', 'to'=>Html::encode($topic['author']['username'])], ['title' => '私信Ta']);
    if ( $me->canReply($topic) ) {
        $topicOp['reply'] = Html::a('<i class="fa fa-reply fa-lg" aria-hidden="true"></i>', null, ['href' => '#reply', 'title' => '回复']);
    }
    if ( $me->canEdit($topic) ) {
        $topicOp['edit'] = Html::a('<i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>', $topicUrl, ['title' => '修改']);
    }
    if ($me->isAdmin()) {
        $topicUrl[0] = 'admin/topic/change-node';
        $topicOp['changeNode'] = Html::a('<i class="fa fa-folder-open-o fa-lg" aria-hidden="true"></i>', $topicUrl, ['title' => '转移节点']);
        $topicUrl[0] = 'admin/topic/delete';
        $topicOp['delete'] = Html::a('<i class="fa fa-trash fa-lg" aria-hidden="true"></i>', $topicUrl, [
            'title' => '删除',
            'data' => [
                'confirm' => '注意：删除后将不会恢复！确认删除！',
                'method' => 'post',
            ]
        ]);
    }
}

$this->title = Html::encode($topic['title']);
?>
<div class="row">
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <span class="fr">
            <?php echo SfHtml::uImgLink($topic['author'], 'large', []); ?>
        </span>
        <?php
            echo Html::a('首页', $indexUrl), '&nbsp;/&nbsp;', Html::a(Html::encode($topic['node']['name']), $nodeUrl);
        ?>
        <h2 class="word-wrap"><?php echo $this->title; ?></h2>
        <small class="gray">
        <?php
            echo '<strong><i class="fa fa-user"></i>', SfHtml::uLink($topic['author']['username']), SfHtml::uGroupRank($topic['author']['score']),'</strong>',
                ' • <i class="fa fa-clock-o"></i>', $formatter->asRelativeTime($topic['created_at']), ' • ', $topic['views'], ' 点击';
            echo ' • 字体 <i class="fa fa-font fa-2x fontsize-plus" title="加大"></i> <i class="fa fa-font fa-lg fontsize-minus" title="缩小"></i>';
            if ( !$isGuest && !empty($topicOp) ) {
                echo '  •  ', implode(' ', $topicOp);
            }
        ?></small>
    </div>
<?php if(!empty($topic['content']['content']) || !empty($topic['tags'])) : ?>
    <div class="panel-body img-zoom content link-external word-wrap <?php echo $whiteWrapClass; ?>">
        <?php
        if(!empty($topic['content']['content'])) {
            $topicShow = true;
            if ( intval($topic['invisible']) === 1 || intval($topic['author']['status']) === User::STATUS_BANNED ) {
                echo '<p class="bg-danger">此主题已被屏蔽</p>';
                $topicShow = false;
            }
            if ( intval($topic['access_auth']) === Topic::TOPIC_ACCESS_REPLY
                    && !$isGuest && !$me->isAuthor($topic['user_id']) && !$me->hasReplied($topic['id']) ) {
                echo '<p class="bg-warning">此主题需要回复才能查看</p>';
                $topicShow = false;
            }
            if ( $topicShow === true || !$isGuest && $me->isAdmin() ) {
                echo $editor->parse($topic['content']['content']);
            }
        }
        if( !empty($topic['tags']) ) {
            echo '<div class="top10">';
            $tags = explode(',', strtolower($topic['tags']));
            foreach($tags as $tag) {
                echo Html::a('<i class="fa fa-tag" aria-hidden="true"></i>'.Html::encode($tag), ['tag/index', 'name'=>$tag], ['class'=>'btn btn-default btn-sm tag']);
            }
            echo '</div>';
        }
        ?>
    </div>
<?php endif; ?>
    <div class="panel-footer bdsharebuttonbox">
<?php
$userOp = [];
if(!$isGuest && $me->isActive()) {
    if ($me->id != $topic['user_id']) {
        $userOp['good'] = Html::a('<i class="fa fa-thumbs-o-up fa-lg" aria-hidden="true"></i><span class="good-num">' . ($topic['good']>0?$topic['good']:'') . '</span>', null, ['id'=>'good-topic-'.$topic['id'], 'title'=>'点赞', 'href' => 'javascript:void(0);',  'data-toggle'=>'modal', 'data-target'=>'#exampleModal', 'data-author'=>Html::encode($topic['author']['username'])]);
    } else {
        $userOp['good'] = '<i class="fa fa-thumbs-o-up fa-lg" aria-hidden="true"></i><span class="good-num">' . ($topic['good']>0?$topic['good']:'') . '</span>';
    }
    $userOp['follow'] = Favorite::checkFollow($me->id, Favorite::TYPE_TOPIC, $topic['id'])?Html::a('<i class="fa fa-ml10 fa-star fa-lg aria-hidden="true""></i><span class="favorite-num">' . ($topic['favorite_count']>0?$topic['favorite_count']:'') . '</span>', null, ['class'=>'favorite', 'title'=>'取消收藏', 'href' => 'javascript:void(0);', 'params'=>'unfavorite topic '. $topic['id']]):Html::a('<i class="fa fa-ml10 fa-star-o fa-lg" aria-hidden="true"></i><span class="favorite-num">' . ($topic['favorite_count']>0?$topic['favorite_count']:'') . '</span>', null, ['class'=>'favorite', 'title'=>'加入收藏', 'href' => 'javascript:void(0);', 'params'=>'favorite topic '. $topic['id']]);
} else {
    $userOp['good'] = '<i class="fa fa-thumbs-o-up fa-lg" aria-hidden="true"></i><span class="good-num">' . ($topic['good']>0?$topic['good']:'') . '</span>';
    $userOp['follow'] = '<i class="fa fa-ml10 fa-star-o fa-lg" aria-hidden="true"></i><span class="favorite-num">' . ($topic['favorite_count']>0?$topic['favorite_count']:'') . '</span>';
}
echo implode('', $userOp) ;
?>
    </div>
</div>

<?php if( intval($topic['comment_count']) > 0 ) : ?>
<ul class="list-group sf-box img-zoom">
    <li class="list-group-item">
<?php echo $topic['comment_count'], '&nbsp;回复&nbsp;|&nbsp;直到&nbsp;', 
    $formatter->asDateTime($topic['replied_at'], 'y-MM-dd HH:mm:ss xxx'); ?>
    </li>
<?php
foreach($comments as $comment){
//  $comment = $comment['comment'];
    $userOp = [];
    $userOpGood = '';
    if ( !$isGuest && $me->isActive() ) {
        $commentUrl = ['comment/edit', 'id'=>$comment['id']];
/*      if ($indexPage > 0) {
            $commentUrl['ip'] = $indexPage;
        }
        if ($nodePage > 0) {
            $commentUrl['np'] = $nodePage;
        }*/
        if ( $me->canEdit($comment, $topic['comment_closed']) ) {
            $userOp['edit'] = Html::a('<i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>', $commentUrl, ['title'=>'修改']);
        }
        if ( $me->isAdmin() ) {
            $commentUrl[0] = 'admin/comment/delete';
            $userOp['delete'] = Html::a('<i class="fa fa-trash fa-lg" aria-hidden="true"></i>', $commentUrl, [
                'title' => '删除',
                'data' => [
                    'confirm' => '注意：删除后将不会恢复！确认删除！',
                    'method' => 'post',
                ]]);
        }
        if ( $me->canReply($topic) ) {
            $userOp['reply'] = Html::a('<i class="fa fa-reply fa-lg" aria-hidden="true"></i>', null, ['title'=>'回复', 'href' => 'javascript:void(0);', 'class'=>'reply-to', 'params'=>Html::encode($comment['author']['username'])]);
        }
        $userOpGood = ' ' . Html::a('<i class="fa fa-thumbs-o-up fa-lg" aria-hidden="true"></i><span class="good-num">' . ($comment['good']>0?$comment['good']:'') . '</span>', null, ['id'=>'good-comment-'.$comment['id'], 'title'=>'点赞', 'href' => 'javascript:void(0);',  'data-toggle'=>'modal', 'data-target'=>'#exampleModal', 'data-author'=>Html::encode($comment['author']['username'])]);
    }
    $userOp['position'] = $comment['position'].'楼';

    echo '<li class="list-group-item media comment-list" id="reply', $comment['position'] ,'">
            <div class="media-left item-avatar">',
                SfHtml::uImgLink($comment['author'], 'normal', []),
            '</div>
             <div class="media-body link-external">
                <div>
                  <small class="fr gray">', implode(' | ', $userOp), '</small>
                  <small class="gray">
                    <i class="fa fa-user" aria-hidden="true"></i><strong>', SfHtml::uLink($comment['author']['username']), SfHtml::uGroupRank($comment['author']['score']), '</strong> ',$comment['author']['comment'],' •  ',
                    '<i class="fa fa-clock-o" aria-hidden="true"></i>', $formatter->asRelativeTime($comment['created_at']), $userOpGood,
                  '</small>
                </div>';
                $commentShow = true;
                if ( $comment['invisible'] == 1 || $comment['author']['status'] == User::STATUS_BANNED ) {
                    echo Alert::widget([
                        'options' => ['class' => 'alert-warning'],
                        'closeButton'=>false,
                        'body' => '此回复已被屏蔽',
                    ]);
                    $commentShow = false;
                }
                if( $commentShow === true || !$isGuest && $me->isAdmin() ) {
                    echo '<div class="comment-content word-wrap ',$whiteWrapClass,'">', $editor->parse($comment['content']) , '</div>';
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
<?php endif; ?>

<?php if( !$isGuest && $me->canReply($topic) ): ?>
<div class="panel panel-default sf-box" id="reply">
    <div class="panel-heading">
        <span class="fr"><a href="#"><i class="fa fa-arrow-up" aria-hidden="true"></i>回到顶部</a></span>添加一条新回复
    </div>
    <div class="panel-body">
<?php $form = ActiveForm::begin(['action' => ['comment/reply', 'id'=>$topic['id']]]);
    echo $form->field(new \app\models\Comment(), 'content')->textArea(['id'=>'editor'])->label(false);
    if($me->canUpload($settings)) {
        $editor->registerUploadAsset($this);
        echo '<div class="form-group"><div id="fileuploader">图片上传</div></div>';
    }
?>
    <div class="form-group">
        <?php echo Html::submitButton('<i class="fa fa-reply" aria-hidden="true"></i>回复', ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end(); ?>
    </div>
</div>
<?php endif; ?>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<?php SfHtml::afterAllPosts($this); ?>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">点赞</h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="checkbox">
            <label>
              <input type="checkbox" class="thanks-author" value="1"> 同时感谢<span class="thanks-to">作者</span>(赞助20积分)
            </label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary good">确认</button>
      </div>
    </div>
  </div>
</div>
