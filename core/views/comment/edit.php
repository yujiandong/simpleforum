<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\components\SfHtml;

$settings = Yii::$app->params['settings'];
$me = Yii::$app->getUser()->getIdentity();

//$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);
$editorClass = '\app\plugins\\'. $settings['editor']. '\\'. ucfirst($settings['editor']);
$editor = new $editorClass();
$editor->registerAsset($this);

$this->title = Yii::t('app', 'Edit {attribute}', ['attribute'=>Yii::t('app', 'Comment')]);
?>

<div class="row">
<div class="col-lg-8 sf-left">

<div class="card sf-box">
	<div class="card-body">
		<?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']]); ?>
		<h3><?php echo Html::encode($topic['title']); ?></h3>
		<small class="gray">
		<?php echo 'by ', SfHtml::uLink($topic['author']['username'], $topic['author']['name']), 
			'  •  ', Yii::$app->getFormatter()->asRelativeTime($topic['created_at']); ?>
		</small>
	</div>
</div>

<div class="card sf-box">
	<div class="card-header sf-box-header sf-navi"><?php echo $this->title; ?></div>
	<div class="card-body">
<?php $form = ActiveForm::begin(); ?>
<?php
	if($me->isAdmin()) {
	 	echo $form->field($comment, 'invisible')->dropDownList([Yii::t('app', 'Public'), Yii::t('app', 'Invisible')])->label(false);
	}
	echo $form->field($comment, 'content')->textArea(['id'=>'editor', 'maxlength'=>30000])->label(false);
	if($me->canUpload($settings)) {
		$editor->registerUploadAsset($this);
		echo '<div class="form-group"><div id="fileuploader">', Yii::t('app', 'Upload Images'), '</div></div>';
	}
?>
    <div class="form-group">
        <?php echo Html::submitButton('<i class="fas fa-edit"></i>'.Yii::t('app', 'Edit'), ['class' => 'btn sf-btn']); ?>
    </div>

<?php ActiveForm::end(); ?>	</div>
</div>

</div>

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
