<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '极简论坛升级';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<div class="box">
		<div class="inner">
			<?php echo $this->title; ?>
		</div>
		<div class="cell bg-info" id="activate"><strong>升级前确认</strong></div>
		<div class="cell">
        	<p>请先备份网站程序，并备份数据库数据。本站不对升级过程中造成的程序或数据损失等负责。</p>
	        <?php
					echo Html::a('已备份，开始升级', ['v1to11'], ['class'=>'btn btn-primary']);
	        ?>
		</div>
	</div>
</div>
<!-- sf-left end -->

</div>
