<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

$this->title = 'Mysql数据库设置';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<div class="box">
		<div class="inner">
			<?= Html::a('极简论坛安装', ['install/install']), '&nbsp;/&nbsp;', $this->title ?>
		</div>
		<div class="cell cell-form">
<?php
if ( !empty($error) ) {
	echo Alert::widget([
	   'options' => ['class' => 'alert-danger'],
	   'closeButton'=>false,
	   'body' => $error,
	]);
}
?>
            <?php $form = ActiveForm::begin([
			    'layout' => 'horizontal',
				'id' => 'form-dbinfo'
			]); ?>
                <?= $form->field($model, 'host') ?>
                <?= $form->field($model, 'dbname') ?>
                <?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'password') ?>
                <?= $form->field($model, 'tablePrefix') ?>
                <div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
                    <?= Html::submitButton('确定', ['class' => 'btn btn-primary', 'name' => 'dbsetting-button']) ?>
					</div>
                </div>
            <?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
<!-- sf-left end -->

</div>
