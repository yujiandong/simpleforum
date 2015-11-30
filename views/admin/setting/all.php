<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '论坛管理';

$items = [
	'配置管理'=>['admin/setting'],
	'节点管理'=>['admin/node'],
	'用户管理'=>['admin/user'],
	'链接管理'=>['admin/link'],
	'邮件测试'=>['admin/setting/test-email'],
];

?>
<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="box">
	<div class="inner">
		<?php
			echo $this->title;
		?>
	</div>
	<div class="cell sf-btn">
<?php
	foreach($items as $k=>$v) {
		echo Html::a($k, $v, ['class'=>'btn btn-default']);
	}
?>
    </div>
</div>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->

</div>
