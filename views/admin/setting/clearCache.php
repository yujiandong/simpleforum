<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

$this->title = '缓存清理';

?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-heading">
		<?php echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;›&nbsp;', Html::a('配置管理', ['admin/setting']), '&nbsp;›&nbsp;', $this->title; ?>
	</div>
	<div class="panel-body sf-box-form">
		缓存清理完毕
	</div>
</div>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>

</div>
