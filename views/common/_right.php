<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

$settings = Yii::$app->params['settings'];
?>


<?php if(!Yii::$app->getUser()->getIsGuest()):

	$me = Yii::$app->getUser()->getIdentity();
	$myInfo = $me->userInfo;
?>
<ul class="list-group sf-box">
  <li class="list-group-item">
		<?php 
			echo Html::img('@web/'.str_replace('{size}', 'normal', $me->avatar), ["alt" => Html::encode($me->username)]) . ' ' . Html::a(Html::encode($me->username), ['user/view', 'username'=>Html::encode($me->username)]);
			if ($me->isWatingActivation()) {
				echo ' <small class="red">[ ', Html::a('未激活', ['user/setting']), ' ]</small>';
			} else if ($me->isWatingVerification()) {
				echo ' <small class="red">[ 管理员验证中 ]</small>';
			}
		?>
		<ul class="list-inline text-center favorite-list">
		  <li><?php echo Html::a($myInfo->favorite_node_count.'<br /><span class="gray">节点收藏</span>', ['my/nodes']); ?></li>
		  <li><?php echo Html::a($myInfo->favorite_topic_count.'<br /><span class="gray">主题收藏</span>', ['my/topics']); ?></li>
		  <li><?php echo Html::a($myInfo->favorite_user_count.'<br /><span class="gray">特别关注</span>', ['my/following']); ?></li>
		</ul>
  </li>
  <li class="list-group-item"><?php echo Html::a('创作新主题', ['topic/new']) ?></li>
  <li class="list-group-item"><?php echo Html::a($me->getNoticeCount().' 条未读提醒', ['user/notifications']); ?></li>
</div>
<?php else: ?>
<ul class="list-group sf-box">
  <li class="list-group-item">
		<strong><?php echo  $settings['site_name']; ?></strong><br />
		<span class="gray"><?php echo  $settings['slogan']; ?></span>
	</li>
  <li class="list-group-item">
		<div class="text-center">
		<p><?php echo Html::a('现在注册', ['site/signup'], ['class' => 'btn btn-primary btn-sm']); ?></p>
		已注册用户请  <?php echo Html::a('登录', ['site/login']); ?>
		</div>
	</li>
</ul>
<?php endif; ?>
