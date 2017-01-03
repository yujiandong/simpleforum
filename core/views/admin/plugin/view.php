<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

$this->title = '插件['.$plugin['pid'].']详细介绍';
$session = Yii::$app->getSession();
$me = Yii::$app->getUser()->getIdentity();

?>

<div class="row">
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item">
        <?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', Html::a('插件管理', ['admin/plugin']), '&nbsp;›&nbsp;', $this->title; ?>
    </li>
    <li class="list-group-item">
		<dl class="dl-horizontal">
		  <dt>插件ID</dt><dd><?php echo $plugin['pid']; ?></dd>
		  <dt>插件名</dt><dd><?php echo $plugin['name']; ?></dd>
		  <dt>版本</dt><dd><?php echo $plugin['version']; ?></dd>
		  <dt>作者</dt><dd><?php echo $plugin['author']; ?></dd>
		  <dt>网址</dt><dd><?php echo $plugin['url']; ?></dd>
		  <dt>描述</dt><dd><?php echo $plugin['description']; ?></dd>
		  <dt></dt><dd>
<?php
	if( $plugin['installed'] == false ) {
		echo Html::a('安装', ['install', 'pid'=>$plugin['pid']], ['class'=>'btn btn-primary']);
	} else {
		echo Html::a('设定', ['settings', 'pid'=>$plugin['pid']], ['class'=>'btn btn-primary']);
		echo Html::a('卸载', ['uninstall', 'pid'=>$plugin['pid']], ['class'=>'btn btn-primary']);
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
