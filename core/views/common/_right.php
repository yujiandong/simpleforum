<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\SfHtml;

$settings = Yii::$app->params['settings'];
?>


<?php if(!Yii::$app->getUser()->getIsGuest()):

    $me = Yii::$app->getUser()->getIdentity();
    $myInfo = $me->userInfo;
?>
<ul class="list-group sf-box">
  <li class="list-group-item">
    <div class="media">
      <?php echo SfHtml::uImg($me); ?>
      <div class="media-body">
        <?php
            echo '<h5 class="mt-0">', SfHtml::uLink($me->username, $me->name, '<br />'), '</h5>';
            if ($me->isWatingActivation()) {
                echo ' <small class="red">[ ', Html::a(Yii::t('app', 'Inactive'), ['my/settings']), ' ]</small>';
            } else if ($me->isWatingVerification()) {
                echo ' <small class="red">[ ' , Yii::t('app', 'Awating verification') , ' ]</small>';
            } else {
                echo SfHtml::uGroup($me->score);
            }
        ?>
      </div>
    </div>
        <ul class="list-inline text-center favorite-list">
          <li class="list-inline-item"><?php echo Html::a($myInfo->favorite_node_count.'<br /><span class="gray">'.Yii::t('app', 'nodes').'</span>', ['my/nodes']); ?></li>
          <li class="list-inline-item"><?php echo Html::a($myInfo->favorite_topic_count.'<br /><span class="gray">'.Yii::t('app', 'topics').'</span>', ['my/topics']); ?></li>
          <li class="list-inline-item"><?php echo Html::a($myInfo->following_count.'<br /><span class="gray">'.Yii::t('app', 'following').'</span>', ['my/following']); ?></li>
        </ul>
  </li>
  <li class="list-group-item py-2"><?php echo Html::a('<i class="fas fa-pencil-alt"></i>'.Yii::t('app', 'Add Topic'), ['topic/new']),' ',Html::a('<i class="fa fa-envelope"></i>'.Yii::t('app', 'SMS'), ['service/sms']); ?></li>
  <li class="list-group-item py-2"><span class="fr"><?php echo Html::a(SfHtml::uScore($me->score), ['my/balance'], ['class'=>'btn btn-sm btn-light']); ?></span>
<?php echo Html::a('<i class="fa'.($me->getNoticeCount()>0?'s':'r').' fa-bell"></i>' . Yii::t('app', 'Notifications{n, plural, =0{} other{(+#)}}', ['n'=>$me->getNoticeCount()]), ['my/notifications']);
if ( intval(Yii::$app->params['settings']['close_register']) === 2 ) {
    echo ' ', Html::a('<i class="fas fa-ticket-alt" aria-hidden="true"></i>' . Yii::t('app', 'Invite Codes') , ['my/invite-codes'], ['title'=>Yii::t('app', 'My Invite Codes')]);
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
        <p><?php echo Html::a('<i class="fa fa-user-plus"></i>'. Yii::t('app', 'Sign up'), ['site/signup'], ['class' => 'btn sf-btn btn-sm']); ?></p>
        <?php echo Yii::t('app', 'Already have an account?'), ' ', Html::a('<i class="fas fa-sign-in-alt"></i>'. Yii::t('app', 'Sign in'), ['site/login']); ?>
<?php
$auths = [];
foreach (Yii::$app->authClientCollection->getClients() as $client){
    if ($settings['auth_setting'][$client->getId()]['show'] != 1) {
        continue;
    }
    if ($client->getId() == 'weixinmp' && $client->type == 'mp') {
        $auths[] = Html::a('<i class="fab fa-lg fa-'.$client->getId().'" aria-hidden="true"></i>', '#', ['onclick'=>'return false;', 'id'=>'weixinmp', 'link'=>Url::to(['site/auth', 'authclient'=>$client->getId()], true)]);
    } else {
        $auths[] = Html::a('<i class="fab fa-lg fa-'.$client->getId().'" aria-hidden="true"></i>', ['site/auth', 'authclient'=>$client->getId()], ['title'=>$client->getTitle()]);
    }
}
        echo ' '.implode(' ', $auths);
?>
        </div>
    </li>
</ul>
<?php endif; ?>
