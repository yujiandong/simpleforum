<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

$this->title = $model->name;
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-heading">
		<?php
			echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', Html::a('节点管理', ['index']), '&nbsp;/&nbsp;节点修改' ;
		?>
	</div>
	<div class="panel-body">
	    <?php echo $this->render('_form', ['model' => $model]) ?>
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
