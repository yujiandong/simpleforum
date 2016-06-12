<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use app\lib\Util;

$settings = Yii::$app->params['settings'];
?>


<?php if(!Yii::$app->getUser()->getIsGuest()):

    $me = Yii::$app->getUser()->getIdentity();
    $myInfo = $me->userInfo;
?>
<ul class="list-group sf-box">
  <li class="list-group-item">
        <?php 
            echo Html::img('@web/'.str_replace('{size}', 'normal', $me->avatar), ['class'=>'img-circle', 'alt' => Html::encode($me->username)]) . ' ' . Html::a(Html::encode($me->username), ['user/view', 'username'=>Html::encode($me->username)]);
            if ($me->isWatingActivation()) {
                echo ' <small class="red">[ ', Html::a('未激活', ['my/settings']), ' ]</small>';
            } else if ($me->isWatingVerification()) {
                echo ' <small class="red">[ 管理员验证中 ]</small>';
            } else {
                echo Util::getGroup($me->score);
            }
        ?>
        <ul class="list-inline text-center favorite-list">
          <li><?php echo Html::a($myInfo->favorite_node_count.'<br /><span class="gray">节点收藏</span>', ['my/nodes']); ?></li>
          <li><?php echo Html::a($myInfo->favorite_topic_count.'<br /><span class="gray">主题收藏</span>', ['my/topics']); ?></li>
          <li><?php echo Html::a($myInfo->favorite_user_count.'<br /><span class="gray">特别关注</span>', ['my/following']); ?></li>
        </ul>
  </li>
  <li class="list-group-item"><?php echo Html::a('<i class="fa fa-pencil"></i>发表新主题', ['topic/new']),' ',Html::a('<i class="fa fa-envelope"></i>发送私信', ['service/sms']); ?></li>
  <li class="list-group-item">
        <span class="fr"><?php echo Html::a(Util::getScore($me->score), ['my/balance'], ['class'=>'btn btn-xs node']); ?></span>
        <?php echo Html::a('<i class="fa fa-bell'.($me->getNoticeCount()>0?'':'-o').'"></i>'.$me->getNoticeCount().' 条提醒', ['my/notifications']);
if ( intval(Yii::$app->params['settings']['close_register']) === 2 ) {
    echo ' ', Html::a('<i class="fa fa-ticket" aria-hidden="true"></i>邀请码', ['my/invite-codes'], ['title'=>'我的邀请码']);
}
        ?>
  </li>
</ul>
<?php else: ?>
<ul class="list-group sf-box">
  <li class="list-group-item">
        <strong><?php echo  $settings['site_name']; ?></strong><br />
        <span class="gray"><?php echo  $settings['slogan']; ?></span>
    </li>
  <li class="list-group-item">
        <div class="text-center">
        <p><?php echo Html::a('<i class="fa fa-user-plus"></i>现在注册', ['site/signup'], ['class' => 'btn btn-primary btn-sm']); ?></p>
        已注册用户请  <?php echo Html::a('<i class="fa fa-sign-in"></i>登录', ['site/login']); ?>
        </div>
    </li>
</ul>
<?php endif; ?>
