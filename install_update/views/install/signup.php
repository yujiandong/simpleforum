<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '注册';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<div class="box">
		<div class="inner">
			<?= Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title ?>
		</div>
		<div class="cell cell-form">
            <?php $form = ActiveForm::begin([
			    'layout' => 'horizontal',
				'id' => 'form-signup'
			]); ?>
                <?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'password_repeat')->passwordInput() ?>
                <div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
                    <?= Html::submitButton('注册', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
					</div>
                </div>
            <?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?= $this->render('@app/views/common/_right') ?>
</div>
<!-- sf-right end -->

</div>
