<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Topic;
use app\models\Siteinfo;
use app\models\Link;
use app\components\SfHtml;

$settings = Yii::$app->params['settings'];
?>


<?php if(!Yii::$app->getUser()->getIsGuest()): 

    $me = Yii::$app->getUser()->getIdentity();
    $myInfo = $me->userInfo;
?>
<ul class="list-group sf-box">
  <li class="list-group-item">
        <?php
            echo SfHtml::uImg($me) . ' ' . SfHtml::uLink($me->username);
            if ($me->isWatingActivation()) {
                echo ' <small class="red">[ ', Html::a('未激活', ['my/settings']), ' ]</small>';
            } else if ($me->isWatingVerification()) {
                echo ' <small class="red">[ 管理员验证中 ]</small>';
            } else {
                echo SfHtml::uGroup($me->score);
            }
        ?>
        <ul class="list-inline text-center favorite-list">
          <li><?php echo Html::a($myInfo->favorite_node_count.'<br /><span class="gray">节点收藏</span>', ['my/nodes']); ?></li>
          <li><?php echo Html::a($myInfo->favorite_topic_count.'<br /><span class="gray">主题收藏</span>', ['my/topics']); ?></li>
          <li><?php echo Html::a($myInfo->favorite_user_count.'<br /><span class="gray">特别关注</span>', ['my/following']); ?></li>
        </ul>
  </li>
  <li class="list-group-item"><?php echo Html::a('<i class="fa fa-pencil"></i>发表新主题', ['topic/new']),' ',Html::a('<i class="fa fa-envelope"></i>发送私信', ['service/sms']); ?></li>
  <li class="list-group-item"><span class="fr"><?php echo Html::a(SfHtml::uScore($me->score), ['my/balance'], ['class'=>'btn btn-xs node']); ?></span>
<?php echo Html::a('<i class="fa fa-bell'.($me->getNoticeCount()>0?'':'-o').'"></i>'.$me->getNoticeCount().' 条提醒', ['my/notifications']);
if ( intval(Yii::$app->params['settings']['close_register']) === 2 ) {
    echo ' ', Html::a('<i class="fa fa-ticket" aria-hidden="true"></i>邀请码', ['my/invite-codes'], ['title'=>'我的邀请码']);
}
?>
  </li>
</ul>
<?php
    if( $me->checkTodaySigned() === false ) :
?>
<ul class="list-group sf-box">
  <li class="list-group-item"><?php echo Html::a('<i class="fa fa-gift" aria-hidden="true"></i>每日签到', ['service/signin']); ?></li>
</ul>
<?php endif; ?>

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
<?php
$auths = [];
foreach (Yii::$app->authClientCollection->getClients() as $client){
    if ($settings['auth_setting'][$client->getId()]['show'] != 1) {
        continue;
    }
    if ($client->getId() == 'weixinmp' && $client->type == 'mp') {
        $auths[] = Html::a('<i class="fa fa-lg fa-'.$client->getId().'" aria-hidden="true"></i>', 'javascript:void(0);', ['id'=>'weixinmp', 'link'=>Url::to(['site/auth', 'authclient'=>$client->getId()], true)]);
    } else {
        $auths[] = Html::a('<i class="fa fa-lg fa-'.$client->getId().'" aria-hidden="true"></i>', ['site/auth', 'authclient'=>$client->getId()], ['title'=>$client->getTitle()]);
    }
}
        echo ' '.implode(' ', $auths);
?>
        </div>
    </li>
</ul>
<?php endif; ?>
<?php
if ( intval($settings['cache_enabled']) === 0 || $this->beginCache('f-index-right', ['duration' => intval($settings['cache_time'])*60])) :
?>
<?php
$hotTopics = Topic::getHotTopics();
if( !empty($hotTopics) ):
?>
<ul class="list-group sf-box">
  <li class="list-group-item gray">24小时内热议主题</li>
<?php
    foreach($hotTopics as $ht) {
        echo '<li class="list-group-item">', Html::a(Html::encode($ht['title']), ['topic/view', 'id'=>$ht['id']]), '</li>';
    }
?>
</ul>
<?php
    endif;
?>
<?php
$siteinfo = Siteinfo::getSiteInfo();
if( !empty($siteinfo) ):
?>
<div class="panel panel-default sf-box">
  <div class="panel-heading gray">社区运行状况</div>
  <div class="panel-body">
        <span class="si-label">注册会员:</span><span class="si-info"><?php echo $siteinfo['users']; ?></span>
        <span class="si-label">节点:</span><span class="si-info"><?php echo $siteinfo['nodes']; ?></span>
        <span class="si-label">主题:</span><span class="si-info"><?php echo $siteinfo['topics']; ?></span>
        <span class="si-label">回复:</span><span class="si-info"><?php echo $siteinfo['comments']; ?></span>
  </div>
</div>
<?php
    endif;
?>
<?php
$links = Link::getLinks();
if( !empty($links) ):
?>
<div class="panel panel-default sf-box">
  <div class="panel-heading gray">友情链接</div>
  <div class="panel-body sf-btn">
<?php
    foreach($links as $link) {
        echo Html::a(Html::encode($link['name']), $link['url'], ['class'=>'btn btn-default btn-sm', 'target'=>'_blank', 'rel' => 'external']);
    }
?>
  </div>
</div>
<?php
    endif;
?>
<?php
if ( intval($settings['cache_enabled']) !== 0 ) {
    $this->endCache();
}
endif;
?>
