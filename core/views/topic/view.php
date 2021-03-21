<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Alert;
use app\components\SfHtml;
use app\components\Util;
use app\models\User;
use app\models\Favorite;
use app\models\Topic;
use app\models\Comment;

$settings = Yii::$app->params['settings'];
$request = Yii::$app->getRequest();
$formatter = Yii::$app->getFormatter();

$editorClass = Yii::$app->params['plugins'][$settings['editor']]['class'];
$editor = new $editorClass();
$editor->registerAsset($this);
\app\assets\LightboxAsset::register($this);

$whiteWrapClass = $settings['editor']=='SmdEditor'?'white-wrap':'';

$indexUrl = ['topic/index'];
$nodeUrl = ['topic/node', 'name'=>$topic['node']['ename']];
$topicUrl = ['topic/edit', 'id'=>$topic['id']];

if ( ($referrer = Util::parseReferrer()) ) {
    $indexPage = ArrayHelper::getValue($referrer, 'index', 0);
    $nodePage = ArrayHelper::getValue($referrer, 'node', 0);
} else {
    $indexPage = intval($request->get('ip', 0));
    $nodePage = intval($request->get('np', 0));
}

if ($indexPage > 1) {
    $indexUrl['p'] = $indexPage;
    $topicUrl['ip'] = $indexPage;
} else if ($nodePage > 1) {
    $nodeUrl['p'] = $nodePage;
    $topicUrl['np'] = $nodePage;
}

$isGuest = Yii::$app->getUser()->getIsGuest();
$topicOp = [];
if(!$isGuest) {
    $me = Yii::$app->getUser()->getIdentity();
    $topicOp['sms'] = Html::a('<i class="fa fa-envelope fa-lg" aria-hidden="true"></i>', ['service/sms', 'to'=>Html::encode($topic['author']['username'])], ['title' => Yii::t('app', 'Send Message')]);
    if ( $me->canReply($topic) ) {
        $topicOp['reply'] = Html::a('<i class="fa fa-comment fa-lg" aria-hidden="true"></i>', null, ['href' => '#reply', 'title' => Yii::t('app', 'Add Comment')]);
    }
    if ( $me->canEdit($topic) ) {
        $topicOp['edit'] = Html::a('<i class="fas fa-edit fa-lg" aria-hidden="true"></i>', $topicUrl, ['title' => Yii::t('app', 'Edit')]);
    }
    if ($me->isAdmin()) {
        $topicUrl[0] = 'admin/topic/change-node';
        $topicOp['changeNode'] = Html::a('<i class="fas fa-folder-open fa-lg" aria-hidden="true"></i>', $topicUrl, ['title' => Yii::t('app', 'Move')]);
        $topicUrl[0] = 'admin/topic/delete';
        $topicOp['delete'] = Html::a('<i class="fas fa-trash-alt fa-lg" aria-hidden="true"></i>', $topicUrl, [
            'title' => Yii::t('app', 'Delete'),
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete it? This operation cannot be undone.'),
                'method' => 'post',
            ]
        ]);
    }
}

$this->title = Html::encode($topic['title']);
?>
<div class="row">
<div class="col-lg-8 sf-left">

<div class="card sf-box sf-box-topic">
    <div class="card-header bg-transparent sf-topic-title">
        <span class="fr">
            <?php echo SfHtml::uImgLink($topic['author'], 'large', []); ?>
        </span>
        <?php
            echo Html::a(Yii::t('app', 'Home'), $indexUrl), '&nbsp;/&nbsp;', Html::a(Html::encode($topic['node']['name']), $nodeUrl);
        ?>
        <h2 class="word-wrap my-3"><?php echo $this->title; ?></h2>
        <small class="gray">
        <?php
            echo '<strong><i class="fa fa-user"></i>', SfHtml::uLink($topic['author']['username'], $topic['author']['name']), SfHtml::uGroupRank($topic['author']['score']),'</strong>',
                ' • <i class="far fa-clock"></i>', $formatter->asRelativeTime($topic['created_at']), ' • ', Yii::t('app', '{0,number} clicks', $topic['views']);
            echo ' • ' .Yii::t('app', 'Font'). ' <i class="fa fa-font fa-2x fontsize-plus" title="'.Yii::t('app', 'Bigger').'"></i> <i class="fa fa-font fa-lg fontsize-minus" title="'.Yii::t('app', 'Smaller').'"></i>';
            if ( !$isGuest && !empty($topicOp) ) {
                echo '  •  ', implode(' ', $topicOp);
            }
        ?></small>
    </div>
<?php if(!empty($topic['content']['content']) || !empty($topic['tags'])) : ?>
    <div class="card-body img-zoom sf-topic-content link-external word-wrap <?php echo $whiteWrapClass; ?>">
        <?php
        if(!empty($topic['content']['content'])) {
            $topicShow = true;
            if ( intval($topic['invisible']) === 1 || intval($topic['author']['status']) === User::STATUS_BANNED ) {
                echo '<p class="bg-danger">' , Yii::t('app', 'This {target} is blocked.', ['target'=>Yii::t('app', 'topic')]) , '</p>';
                $topicShow = false;
            }
            if ( intval($topic['access_auth']) === Topic::TOPIC_ACCESS_REPLY
                    && !$isGuest && !$me->isAuthor($topic['user_id']) && !$me->hasReplied($topic['id']) ) {
                echo '<p class="bg-warning">' , Yii::t('app', 'Topics only shown for signed in users') , '</p>';
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
                echo Html::a('<i class="fa fa-tag" aria-hidden="true"></i>'.Html::encode($tag), ['tag/index', 'name'=>$tag], ['class'=>'btn btn-sm btn-light tag']);
            }
            echo '</div>';
        }
        ?>
    </div>
<?php endif; ?>
    <div class="card-footer bg-transparent">
<?php
$userOp = [];
if(!$isGuest && $me->isActive()) {
    if ($me->id != $topic['user_id']) {
        $userOp['good'] = Html::a('<i class="far fa-thumbs-up fa-lg" aria-hidden="true"></i><span class="good-num">' . ($topic['good']>0?$topic['good']:'') . '</span>', null, ['id'=>'good-topic-'.$topic['id'], 'title'=>Yii::t('app', 'Good'), 'href' => '#', 'onclick'=> 'return false;',  'data-toggle'=>'modal', 'data-target'=>'#exampleModal', 'data-author'=>Html::encode($topic['author']['username'])]);
    } else {
        $userOp['good'] = '<i class="far fa-thumbs-up fa-lg" aria-hidden="true"></i><span class="good-num">' . ($topic['good']>0?$topic['good']:'') . '</span>';
    }
    $userOp['follow'] = Favorite::checkFollow($me->id, Favorite::TYPE_TOPIC, $topic['id'])?Html::a('<i class="fas fa-ml10 fa-star fa-lg aria-hidden="true""></i><span class="favorite-num">' . ($topic['favorite_count']>0?$topic['favorite_count']:'') . '</span>', null, ['class'=>'favorite', 'title'=>Yii::t('app', 'Cancel Favorite'), 'href' => '#', 'onclick'=> 'return false;', 'params'=>'unfavorite topic '. $topic['id']]):Html::a('<i class="far fa-ml10 fa-star fa-lg" aria-hidden="true"></i><span class="favorite-num">' . ($topic['favorite_count']>0?$topic['favorite_count']:'') . '</span>', null, ['class'=>'favorite', 'title'=>Yii::t('app', 'Favorite'), 'href' => '#', 'onclick'=> 'return false;', 'params'=>'favorite topic '. $topic['id']]);
} else {
    $userOp['good'] = '<i class="far fa-thumbs-up fa-lg" aria-hidden="true"></i><span class="good-num">' . ($topic['good']>0?$topic['good']:'') . '</span>';
    $userOp['follow'] = '<i class="far fa-ml10 fa-star fa-lg" aria-hidden="true"></i><span class="favorite-num">' . ($topic['favorite_count']>0?$topic['favorite_count']:'') . '</span>';
}
echo implode('', $userOp) ;
?>
    </div>
</div>

<?php if( intval($topic['comment_count']) > 0 ) : ?>
<ul class="list-group sf-box img-zoom sf-box-comments">
    <li class="list-group-item sf-box-header">
<?php echo Yii::t('app', '{n, plural, =0{no comments} =1{# comment} other{# comments}}', ['n'=>intval($topic['comment_count'])]), '&nbsp;|&nbsp;' . Yii::t('app', 'until {time}', ['time'=>$formatter->asDateTime($topic['replied_at'], 'y-MM-dd HH:mm:ssZ')]); ?>
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
        }
*/
        if ( $me->canEdit($comment, $topic['comment_closed']) ) {
            $userOp['edit'] = Html::a('<i class="fas fa-edit fa-lg" aria-hidden="true"></i>', $commentUrl, ['title'=>Yii::t('app', 'Edit')]);
        }
        if ( $me->isAdmin() ) {
            $commentUrl[0] = 'admin/comment/delete';
            $userOp['delete'] = Html::a('<i class="fas fa-trash-alt fa-lg" aria-hidden="true"></i>', $commentUrl, [
                'title' => Yii::t('app', 'Delete'),
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete it? This operation cannot be undone.'),
                    'method' => 'post',
                ]]);
        }
        if ( $me->canReply($topic) ) {
            $userOp['reply'] = Html::a('<i class="fa fa-reply fa-lg" aria-hidden="true"></i>', null, ['title'=>Yii::t('app', 'Add Comment'), 'href' => '#', 'onclick'=> 'return false;', 'class'=>'reply-to', 'params'=>Html::encode($comment['author']['username'])]);
        }
        $userOpGood = ' ' . Html::a('<i class="fa fa-thumbs-o-up fa-lg" aria-hidden="true"></i><span class="good-num">' . ($comment['good']>0?$comment['good']:'') . '</span>', null, ['id'=>'good-comment-'.$comment['id'], 'title'=>Yii::t('app', 'Good'), 'href' => '#', 'onclick'=> 'return false;',  'data-toggle'=>'modal', 'data-target'=>'#exampleModal', 'data-author'=>Html::encode($comment['author']['username'])]);
    }
    $userOp['position'] = Yii::t('app', '#{0,number}', $comment['position']);

    echo '<li class="list-group-item media comment-list" id="reply', $comment['position'] ,'">
            <div class="item-avatar">',
                SfHtml::uImgLink($comment['author'], 'normal', []),
            '</div>
             <div class="media-body link-external">
                <div>
                  <small class="fr gray">', implode(' | ', $userOp), '</small>
                  <small class="gray">
                    <i class="fa fa-user" aria-hidden="true"></i><strong>', SfHtml::uLink($comment['author']['username'], $comment['author']['name']), SfHtml::uGroupRank($comment['author']['score']), '</strong> ',$comment['author']['comment'],' •  ',
                    '<i class="far fa-clock" aria-hidden="true"></i>', $formatter->asRelativeTime($comment['created_at']), $userOpGood,
                  '</small>
                </div>';
                $commentShow = true;
                if ( $comment['invisible'] == 1 || $comment['author']['status'] == User::STATUS_BANNED ) {
                    echo Alert::widget([
                        'options' => ['class' => 'alert-warning'],
                        'closeButton'=>false,
                        'body' => Yii::t('app', 'This {target} is blocked.', ['target'=>Yii::t('app', 'comment')]),
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
<?php endif; ?>

<?php if( !$isGuest && $me->canReply($topic) ): ?>
<div class="card sf-box" id="reply">
    <div class="card-header sf-box-header">
        <span class="fr"><a href="#"><i class="fa fa-arrow-up" aria-hidden="true"></i><?php echo Yii::t('app', 'Go to top'); ?></a></span><?php echo Yii::t('app', 'Add Comment'); ?>
    </div>
    <div class="card-body">
<?php $form = ActiveForm::begin(['action' => ['comment/reply', 'id'=>$topic['id']]]);
    $model = new Comment();
    echo $form->field($model, 'content')->textArea(['id'=>'editor'])->label(false);
    if($me->canUpload($settings)) {
        $editor->registerUploadAsset($this);
        echo '<div class="form-group"><div id="fileuploader">'.Yii::t('app', 'Upload Images').'</div></div>';
    }
?>
<?php
         $captcha = ArrayHelper::getValue(Yii::$app->params, 'settings.captcha', '');
        if(!empty($captcha) && ($plugin=ArrayHelper::getValue(Yii::$app->params, 'plugins.' . $captcha, []))) {
            $plugin['class']::captchaWidget('newcomment', $form, $model, Url::toRoute(['comment/reply', 'id'=>$topic['id']]), $plugin);
        }
?>
    <div class="form-group">
        <?php echo Html::submitButton('<i class="fa fa-comment" aria-hidden="true"></i>'.Yii::t('app', 'Add Comment'), ['class' => 'btn sf-btn']); ?>
    </div>
<?php ActiveForm::end(); ?>
    </div>
</div>
<?php endif; ?>

</div>

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<?php SfHtml::afterAllPosts($this); ?>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel"><?php echo Yii::t('app', 'Good'); ?></h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="checkbox">
            <label>
              <input type="checkbox" class="thanks-author" value="1"> <?php echo Yii::t('app', 'Thank <span class="thanks-to">author</span> and donate 20 points.'); ?>
            </label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn sf-btn good"><?php echo Yii::t('app', 'Submit'); ?></button>
      </div>
    </div>
  </div>
</div>
