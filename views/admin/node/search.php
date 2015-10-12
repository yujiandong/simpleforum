<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;

$this->title = '节点管理';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="box">
	<div class="inner">
		<?php
			echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
		?>
	</div>
            <?php $form = ActiveForm::begin([
			    'layout' => 'horizontal',
				'id' => 'form-setting',
			    'fieldConfig' => [
//			        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
			        'horizontalCssClasses' => [
			            'label' => 'col-sm-2',
//			            'offset' => 'col-sm-offset-4',
			            'wrapper' => 'col-sm-10',
			            'error' => '',
			            'hint' => 'col-sm-offset-2 col-sm-10',
			        ],
			    ],
			]); ?>
	<div class="cell bg-info"><strong>检索</strong>
	</div>
	<div class="cell">
<?php
		echo $form->field($model, "name");
?>
                <div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
                    <?= Html::submitButton('检索', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
					</div>
                </div>
	</div>
<?php
ActiveForm::end();
?>
	<div class="cell bg-info"><strong>节点</strong>
	</div>
	<div class="cell">
		<ul>
		<?php
			if( !empty($node) ) {
				echo '<li>[', $node['id'], ']&nbsp;', Html::encode($node['name']), '&nbsp;(', $node['ename'], ')&nbsp;|&nbsp;', Html::a('修改', ['admin/node/edit', 'id'=>$node['id']]), '</li>';
			} else {
				echo '检索的节点不存在';
			}
		?>
		</ul>
	</div>
</div>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?= $this->render('@app/views/common/_admin-right') ?>
</div>
<!-- sf-right end -->

</div>
