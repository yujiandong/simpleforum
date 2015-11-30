<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

$settings = Yii::$app->params['settings'];
$this->title = '创建新主题';
?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="box">
	<div class="inner">
		<?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', 
			Html::a(Html::encode($node['name']), ['topic/node', 'name'=>$node['ename']]), 
			'&nbsp;/&nbsp;', $this->title; ?>
	</div>
	<div class="cell">
    <?php echo $this->render('_form', [
        'model' => $model,
        'content' => $content,
		'action' => 'add',
    ]); ?>
	</div>
</div>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
