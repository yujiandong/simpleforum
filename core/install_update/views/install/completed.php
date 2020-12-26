<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app/admin', 'Installation completed successfully.');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<div class="panel panel-default sf-box">
		<div class="panel-heading">
			<?php echo Html::a(Yii::t('app/admin', 'Install SimpleForum'), ['index']), '&nbsp;/&nbsp;', $this->title; ?>
		</div>
		<div class="panel-body">
				<h2><?php echo Yii::t('app/admin', 'Please goto Admin Panel and complete forum\'s settings.'); ?></h2>
				<?php echo Html::a(Yii::t('app', 'Admin Panel'), ['/admin/setting/all']); ?>
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
