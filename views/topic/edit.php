<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

$this->title = '修改主题';

$request = Yii::$app->getRequest();
$me = Yii::$app->getUser()->getIdentity();

$indexUrl = ['topic/index'];
$nodeUrl = ['topic/node', 'name'=>$model['node']['ename']];
if ($request->get('ip', 1) > 1) {
	$indexUrl['p'] = $request->get('ip', 1);
}
if ($request->get('np', 1) > 1) {
	$nodeUrl['p'] = $request->get('np', 1);
}
?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="box">
	<div class="inner">
		<?= Html::a('首页', $indexUrl), '&nbsp;/&nbsp;', Html::a(Html::encode($model['node']['name']), $nodeUrl), '&nbsp;/&nbsp;', $this->title ?>
	</div>
	<div class="cell">
    <?= $this->render('_form', [
        'model' => $model,
        'content' => $content,
		'action' => 'edit',
    ]) ?>
	</div>
</div>

</div>

<div class="col-md-4 sf-right">
<?= $this->render('@app/views/common/_right') ?>
</div>

</div>
