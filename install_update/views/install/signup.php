<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '创建管理员帐号';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<div class="box">
		<div class="inner">
			<?php echo Html::a('极简论坛安装', ['index']), '&nbsp;/&nbsp;', $this->title; ?>
		</div>
		<div class="cell cell-form">
            <?php $form = ActiveForm::begin([
			    'layout' => 'horizontal',
				'id' => 'form-admin-signup'
			]); ?>
                <?php echo $form->field($model, 'username'); ?>
                <?php echo $form->field($model, 'email'); ?>
                <?php echo $form->field($model, 'password')->passwordInput(); ?>
                <?php echo $form->field($model, 'password_repeat')->passwordInput(); ?>
                <div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
                    <?php echo Html::submitButton('创建', ['class' => 'btn btn-primary']); ?>
					</div>
                </div>
            <?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
<!-- sf-left end -->

<!-- sf-right start -->
<!-- sf-right end -->

</div>
