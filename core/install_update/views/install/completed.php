<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

$this->title = Yii::t('app/admin', 'Installation completed successfully.');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-lg-8 sf-left">
	<div class="card sf-box">
		<div class="card-header bg-transparent">
			<?php echo Html::a(Yii::t('app/admin', 'Install SimpleForum'), ['index']), '&nbsp;/&nbsp;', $this->title; ?>
		</div>
		<div class="card-body">
				<h2><?php echo Yii::t('app/admin', 'Please goto Admin Panel and complete forum\'s settings.'); ?></h2>
				<?php echo Html::a(Yii::t('app', 'Admin Panel'), ['/admin/setting/all']); ?>
		</div>
	</div>
</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
