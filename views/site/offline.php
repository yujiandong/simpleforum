<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\Alert;

$this->title = '论坛暂时关闭';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<div class="box">
		<div class="inner">
			<?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
		</div>
		<div class="cell cell-form">
<?php echo Alert::widget([
	   'options' => ['class' => 'alert-danger'],
	   'closeButton'=>false,
	   'body' => Yii::$app->params['settings']['offline_msg'],
	]);
?>
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
