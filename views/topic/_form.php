<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$settings = Yii::$app->params['settings'];

$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);
$editor->registerAsset($this);
$editor->registerTagItAsset($this);

/*
$this->registerAssetBundle('app\assets\JqueryUploadFileAsset');
$this->registerJs('$("#fileuploader").uploadFile({
	url:"'.Url::to(['user/upload']).'",
	fileName:"UploadForm[files]",
	returnType:"json",
	maxFileCount:4,
	maxFileSize:1024*1024,
	onSuccess:function(files,data,xhr,pd) {
		pd.filename.append("  <a id=\""+data+"\" class=\"insert-image\" href=\"javascript:void(0);\">插入图片</a>");
	}
});');
*/
?>

<?php $form = ActiveForm::begin(); ?>
    <?php
		if( $action === 'edit' && Yii::$app->getUser()->getIdentity()->isAdmin()) {
			echo '<div class="row">',
		 		'<div class="col-md-2 col-xs-4">', $form->field($model, 'invisible')->checkbox(), '</div>',
		 		'<div class="col-md-2 col-xs-4">', $form->field($model, 'comment_closed')->checkbox(), '</div>',
		 		'<div class="col-md-2 col-xs-4">', $form->field($model, 'alltop')->checkbox(), '</div>',
		 		'<div class="col-md-2 col-xs-4">', $form->field($model, 'top')->checkbox(), '</div>',
			'</div>';
		}
	?>
	<p>主题标题 <span class="gray">( 如果标题能够表达完整内容，主题内容可为空 )</span></p>
    <?php echo $form->field($model, 'title')->textArea(['rows' => '4', 'maxlength'=>120])->label(false); ?>
	<p>主题内容</p>
	<?php echo $form->field($content, 'content')->textArea(['id'=>'editor', 'maxlength'=>30000])->label(false); ?>
	<p>标签 <span class="gray">( 最多4个，以空格分隔 )</span></p>
	<?php echo $form->field($model, 'tags')->textInput(['id'=>'tags', 'maxlength'=>60])->label(false); ?>
<?php
	if( Yii::$app->getUser()->getIdentity()->canUpload($settings) ) {
		$editor->registerUploadAsset($this);
		echo '<div class="form-group"><div id="fileuploader">图片上传</div></div>';
	}
?>
	<div class="form-group">
		<?php echo Html::submitButton($model->isNewRecord ? '创建' : '修改', ['class' => 'btn btn-primary']); ?>
	</div>
<?php ActiveForm::end(); ?>
