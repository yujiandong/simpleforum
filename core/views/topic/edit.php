<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

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

$this->title = Yii::t('app', 'Edit a topic');

?>

<div class="row">
<div class="col-lg-8 sf-left">

<div class="card sf-box">
	<div class="card-header sf-box-header sf-navi">
		<?php echo Html::a(Yii::t('app', 'Home'), $indexUrl), '&nbsp;/&nbsp;', Html::a(Html::encode($model['node']['name']), $nodeUrl), '&nbsp;/&nbsp;', $this->title; ?>
	</div>
	<div class="card-body">
    <?php echo $this->render('_form', [
        'model' => $model,
        'content' => $content,
		'action' => 'edit',
    ]); ?>
	</div>
</div>

</div>

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
