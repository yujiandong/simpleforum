<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '论坛升级完成';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<div class="panel panel-default sf-box">
		<div class="panel-heading">
			<?php echo $this->title; ?>
		</div>
		<div class="panel-body">
			<h2>论坛升级完成</h2>
			<p>1. 可以删除目录： lib/phpanalysis</p>
			<p>2. <?php echo Html::a('登录管理后台更新论坛配置', ['/admin/setting/all']); ?></p>
		</div>
	</div>
</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
