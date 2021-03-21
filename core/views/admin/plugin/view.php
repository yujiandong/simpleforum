<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

$this->title = Yii::t('app/admin', 'Abount Plugin[{pid}]', ['pid' => $plugin['pid']]);
$session = Yii::$app->getSession();
$me = Yii::$app->getUser()->getIdentity();

?>

<div class="row">
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item sf-box-header sf-navi">
        <?php
			echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', Html::a(Yii::t('app/admin', 'Plugins'), ['index']), '&nbsp;/&nbsp;', $this->title;
        ?>
    </li>
    <li class="list-group-item">
		<dl class="row">
		  <dt class="col-sm-3 text-right"><?php echo Yii::t('app/admin', 'Plugin id'); ?></dt><dd class="col-sm-9"><?php echo $plugin['pid']; ?></dd>
		  <dt class="col-sm-3 text-right"><?php echo Yii::t('app/admin', 'Plugin name'); ?></dt><dd class="col-sm-9"><?php echo $plugin['name']; ?></dd>
		  <dt class="col-sm-3 text-right"><?php echo Yii::t('app', 'Version'); ?></dt><dd class="col-sm-9"><?php echo $plugin['version']; ?></dd>
		  <dt class="col-sm-3 text-right"><?php echo Yii::t('app', 'Author'); ?></dt><dd class="col-sm-9"><?php echo $plugin['author']; ?></dd>
		  <dt class="col-sm-3 text-right"><?php echo Yii::t('app', 'Homepage'); ?></dt><dd class="col-sm-9"><?php echo $plugin['url']; ?></dd>
		  <dt class="col-sm-3 text-right"><?php echo Yii::t('app', 'Description'); ?></dt><dd class="col-sm-9"><?php echo $plugin['description']; ?></dd>
		  <dt class="col-sm-3 text-right"></dt><dd class="col-sm-9">
<?php
	if( $plugin['installed'] == false ) {
		echo Html::a(Yii::t('app/admin', 'Install'), ['install', 'pid'=>$plugin['pid']], ['class'=>'btn sf-btn']);
	} else {
		echo Html::a(Yii::t('app/admin', 'Setting'), ['settings', 'pid'=>$plugin['pid']], ['class'=>'btn sf-btn']);
		echo ' ', Html::a(Yii::t('app/admin', 'Uninstall'), ['uninstall', 'pid'=>$plugin['pid']], ['class'=>'btn sf-btn']);
	}
?>
		  </dd>
		</dl>
    </li>
</ul>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
