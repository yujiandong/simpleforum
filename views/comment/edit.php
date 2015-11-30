<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$settings = Yii::$app->params['settings'];
$me = Yii::$app->getUser()->getIdentity();

$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);
$editor->registerAsset($this);

$this->title = '修改回复';
?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="box">
	<div class="cell topic-header">
		<?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', 
			Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']]); ?>
		<h3><?php echo Html::encode($topic['title']); ?></h3>
		<small class="gray">
		<?php echo 'by ', Html::a(Html::encode($topic['author']['username']), ['user/view', $topic['user_id']]), 
			'  •  ', Yii::$app->getFormatter()->asRelativeTime($topic['created_at']); ?>
		</small>
	</div>
</div>

<div class="box topic-comment">
	<div class="inner">
		修改回复
	</div>
	<div class="cell">
<?php $form = ActiveForm::begin(); ?>

<?php
	if(Yii::$app->getUser()->getIdentity()->isAdmin()) {
	 	echo $form->field($comment, 'invisible')->dropDownList(['公开回复', '屏蔽回复'])->label(false);
	}
	echo $form->field($comment, 'content')->textArea(['id'=>'editor', 'maxlength'=>30000])->label(false);
	if($me->canUpload($settings)) {
		$editor->registerUploadAsset($this);
		echo '<div class="form-group"><div id="fileuploader">图片上传</div></div>';
	}
?>
    <div class="form-group">
        <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?>
    </div>

<?php ActiveForm::end(); ?>	</div>
</div>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
