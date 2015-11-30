<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use app\models\Topic;
use app\models\Siteinfo;
use app\models\Link;

$settings = Yii::$app->params['settings'];
?>


<?php if(!Yii::$app->getUser()->getIsGuest()): 

	$me = Yii::$app->getUser()->getIdentity();
	$myInfo = $me->userInfo;
?>
<div class="box">
	<div class="cell">
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
	</div>
	<div class="cell"><?php echo Html::a('创作新主题', ['topic/new']); ?>
	</div>
	<div class="cell"><?php echo Html::a($me->getNoticeCount().' 条未读提醒', ['user/notifications']); ?>
	</div>
</div>
<?php else: ?>
<div class="box">
	<div class="cell">
		<strong><?php echo $settings['site_name'] ?></strong><br />
		<span class="gray"><?php echo $settings['slogan']; ?></span>
	</div>
	<div class="inner">
		<div class="text-center">
		<p><?php echo Html::a('现在注册', ['site/signup'], ['class' => 'btn btn-primary btn-sm']); ?></p>
		已注册用户请  <?php echo Html::a('登录', ['site/login']); ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php
if ( intval($settings['cache_enabled']) === 0 || $this->beginCache('f-index-right', ['duration' => intval($settings['cache_time'])*60])) :
?>
<?php
$hotTopics = Topic::getHotTopics();
if( !empty($hotTopics) ):
?>
<div class="box">
	<div class="inner gray">24小时内热议主题
	</div>
<?php
	foreach($hotTopics as $ht):
?>
	<div class="cell hot-topics-title">
		<?php echo Html::a(Html::encode($ht['title']), ['topic/view', 'id'=>$ht['id']]); ?>
	</div>
<?php
	endforeach;
?>
</div>
<?php
	endif;
?>
<?php
$siteinfo = Siteinfo::getSiteInfo();
if( !empty($siteinfo) ):
?>
<div class="box">
	<div class="inner gray">社区运行状况
	</div>
	<div class="cell clearfix">
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
<div class="box">
	<div class="inner gray">友情链接
	</div>
	<div class="cell sf-btn">
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
