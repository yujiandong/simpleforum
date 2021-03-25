<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\Alert;
use app\components\SfHtml;
use app\models\Favorite;
use app\models\User;

$this->title = $user['name'].'(@'.$user['username'].')';
$settings = Yii::$app->params['settings'];
$editorClass = Yii::$app->params['plugins'][$settings['editor']]['class'];
$editor = new $editorClass();

$whiteWrapClass = $settings['editor']=='SmdEditor'?'white-wrap':'';

$fomatter = Yii::$app->getFormatter();
$isGuest = Yii::$app->getUser()->getIsGuest();
if (!$isGuest) {
    $me = Yii::$app->getUser()->getIdentity();
}

$userOp = [];
if (!$isGuest && $me->isAdmin() && $me->id != $user['id']) {
    $userOp['manage'] = Html::a('<i class="fas fa-edit fa-lg" aria-hidden="true"></i>', ['admin/user/info', 'id'=>$user['id']], ['title'=>Yii::t('app', 'Manage')]);
}

if (!$isGuest && $me->isActive() && $me->id != $user['id']) {
    $userOp['sms'] = Html::a('<i class="fa fa-envelope fa-lg" aria-hidden="true"></i>', ['service/sms', 'to'=>$user['username']], ['title' => Yii::t('app', 'Send Message')]);
    $userOp['follow'] = Favorite::checkFollow($me->id, Favorite::TYPE_USER, $user['id'])?Html::a('<i class="fas fa-star fa-lg aria-hidden="true""></i><span class="favorite-num">' . ($user['userInfo']['follower_count']>0?$user['userInfo']['follower_count']:'') . '</span>', null, ['class'=>'favorite', 'title'=>Yii::t('app', 'Unfollow'), 'href' => '#', 'onclick'=> 'return false;', 'params'=>'unfavorite user '. $user['id']]):Html::a('<i class="far fa-star fa-lg" aria-hidden="true"></i><span class="favorite-num">' . ($user['userInfo']['follower_count']>0?$user['userInfo']['follower_count']:'') . '</span>', null, ['class'=>'favorite', 'title'=>Yii::t('app', 'Follow'), 'href' => '#', 'onclick'=> 'return false;', 'params'=>'favorite user '. $user['id']]);
}

?>

<div class="row">
<div class="col-lg-8 sf-left">

<div class="card sf-box">
    <div class="card-header bg-transparent media">
        <div class="item-largeavatar">
            <?php echo SfHtml::uImg($user, 'large'); ?>
        </div>
        <div class="media-body">
            <span class="fr"><?php echo implode(' ', $userOp); ?></span>
            <h2 class="media-heading"><?php echo $user['name'], '<br /><small class="gray">@', $user['username'], '</small><small>', SfHtml::uGroup($user['score']), '</small>'; ?></h2>
            <p class="gray"><i class="fas fa-calendar-alt" aria-hidden="true"></i> <?php echo Yii::t('app', 'The {n, plural, =1{#st} =2{#nd} =3{#rd} other{#th}} member, joined on {date}.', ['n'=>$user['id'], 'date'=>$fomatter->asDate($user['created_at'], 'y-MM-dd')]); ?>
            </p>
        </div>
    </div>
    <?php if( !empty($user['userInfo']['about']) || !empty($user['userInfo']['website']) ) : ?>
    <div class="card-body link-external">
        <?php echo empty($user['userInfo']['about'])?'':'<p>'.Html::encode($user['userInfo']['about']).'</p>'; ?>
        <?php echo empty($user['userInfo']['website'])?'':'<i class="fa fa-link" aria-hidden="true"></i> '.Html::a(Html::encode($user['userInfo']['website']), $user['userInfo']['website'], ['target'=>'_blank', 'rel' => 'external']); ?>
    </div>
    <?php endif ?>
</div>

<ul class="list-group sf-box sf-box-topics">
    <li class="list-group-item sf-box-header"><?php echo Yii::t('app', '{username}\'s Latest Topics', ['username'=>$user['username']]); ?></li>
<?php
foreach($user['topics'] as $topic){
    echo '<li class="list-group-item">
                <h5 class="media-heading">',
                Html::a(Html::encode($topic['title']), ['topic/view', 'id'=>$topic['id']]),
                '</h5>
                <div class="small gray">';
    if($topic['comment_count'] > 0){
        $gotopage = ceil($topic['comment_count']/$settings['comment_pagesize']);
        $url = ['topic/view', 'id'=>$topic['id']];
        if($gotopage > 1){
            $url['p'] = $gotopage;
        }
        echo Html::a($topic['comment_count'], $url, ['class'=>'badge fr count-info']);
    }
                echo Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']], ['class'=>'btn btn-sm btn-light small']),
                ' •  ', $fomatter->asRelativeTime($topic['replied_at']);
        if ($topic['comment_count']>0) {
                echo '<span class="item-lastreply"> • <i class="fa fa-comment" aria-hidden="true"></i>', SfHtml::uLink($topic['lastReply']['username']) , '</span>';
        }
                echo '</div>';
    echo '</li>';
}
?>
    <li class="list-group-item">
        » <?php echo Html::a(Yii::t('app', '{username}\'s All Topics', ['username'=>$user['username']]), ['topics', 'username'=>$user['username']]); ?>
    </li>
</ul>

<ul class="list-group sf-box sf-box-comments">
    <li class="list-group-item sf-box-header">
        <?php echo Yii::t('app', '{username}\'s Latest Comments', ['username'=>$user['username']]); ?>
    </li>
<?php
foreach($user['comments'] as $comment) :
?>
    <li class="list-group-item gray small list-group-item-info">
        <span class='fr'><?php echo $fomatter->asRelativeTime($comment['created_at']); ?></span>
        <?php echo Yii::t('app', 'Commented on {author}\'s topic', ['author'=>Html::encode($comment['topic']['author']['username'])]); ?> › <?php echo Html::a(Html::encode($comment['topic']['title']), ['topic/view', 'id'=>$comment['topic_id']]); ?>
    </li>
    <li class="list-group-item word-wrap <?php echo $whiteWrapClass; ?>">
    <?php
        if ( $comment['invisible'] == 1 || $user['status'] == User::STATUS_BANNED ) {
            echo Alert::widget([
                'options' => ['class' => 'alert-warning'],
                'closeButton'=>false,
                'body' => Yii::t('app', 'This {target} is blocked.', ['target'=>Yii::t('app', 'comment')]),
            ]);
            if (!$isGuest && $me->isAdmin()) {
                echo $editor->parse($comment['content']);
            }
        } else {
            echo $editor->parse($comment['content']);
        }
    ?>
    </li>
<?php endforeach; ?>
    <li class="list-group-item">
        » <?php echo Html::a(Yii::t('app', '{username}\'s All Comments', ['username'=>$user['username']]), ['comments', 'username'=>$user['username']]); ?>
    </li>
</ul>

</div>

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
