<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

extract($check->result);

$this->title = Yii::t('app/admin', 'Check server\'s environment');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<ul class="list-group sf-box">
		<li class="list-group-item">
			<?php echo Html::a(Yii::t('app/admin', 'Install SimpleForum'), ['index']), '&nbsp;/&nbsp;', $this->title; ?>
		</li>
		<li class="list-group-item list-group-item-info"><strong><?php echo Yii::t('app/admin', 'Server\'s environment'); ?></strong></li>
		<li class="list-group-item">
        	<p><?php echo $check->getServerInfo() . ' ' . $check->getNowDate() ?></p>
		</li>
		<li class="list-group-item list-group-item-info"><strong><?php echo Yii::t('app/admin', 'Check result'); ?></strong></li>
		<li class="list-group-item">
	        <?php if ($summary['errors'] > 0): ?>
	            <div class="alert alert-danger">
	                <strong><?php echo Yii::t('app/admin', 'Your server doesn\'t satisfy minimum requirements. Please check the error items below.'); ?></strong>
	            </div>
	        <?php elseif ($summary['warnings'] > 0): ?>
	            <div class="alert alert-info">
	                <strong><?php echo Yii::t('app/admin', 'Your server satisfies minimum requirements. Please check the warning items below.'); ?></strong>
	            </div>
	        <?php else: ?>
	            <div class="alert alert-success">
	                <strong><?php echo Yii::t('app/admin', 'Your server satisfies all requirements.'); ?></strong>
	            </div>
	        <?php endif; ?>
	        <?php
				if ($summary['errors'] == 0) {
					Yii::$app->getSession()->set('install-step', 1);
					echo Html::a(Yii::t('app/admin', 'Next step: Database setting'), ['db-setting'], ['class'=>'btn btn-primary']);
				}
	        ?>
		</li>
		<li class="list-group-item list-group-item-info"><strong><?php echo Yii::t('app/admin', 'Check list'); ?></strong></li>
		<li class="list-group-item">
	        <table class="table table-bordered">
	            <tr><th><?php echo Yii::t('app/admin', 'Requirement'); ?></th><th><?php echo Yii::t('app/admin', 'Result'); ?></th><th><?php echo Yii::t('app/admin', 'Comment'); ?></th></tr>
	            <?php foreach ($requirements as $requirement): ?>
	            <tr class="<?php echo $requirement['condition'] ? 'success' : ($requirement['mandatory'] ? 'danger' : 'warning') ?>">
	                <td>
	                <?php echo $requirement['name'] ?>
	                </td>
	                <td>
	                <span class="result"><?php echo $requirement['condition'] ? Yii::t('app/admin', 'OK') : ($requirement['mandatory'] ? Yii::t('app/admin', 'Error') : Yii::t('app/admin', 'Warning')) ?></span>
	                </td>
	                <td>
	                <?php echo $requirement['memo'] ?>
	                </td>
	            </tr>
	            <?php endforeach; ?>
	        </table>
		</li>
	</ul>
</div>
<!-- sf-left end -->

</div>
