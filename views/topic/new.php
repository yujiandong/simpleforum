<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Node;

$settings = Yii::$app->params['settings'];

$this->registerAssetBundle('app\assets\Select2Asset');
$this->registerJs('$(".nodes-select2").select2({placeholder:"请选择一个节点",allowClear: true});');

$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);
$editor->registerAsset($this);

$this->title = '创建新主题';
?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="box">
	<div class="inner">
		<?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
	</div>
	<div class="cell">
<?php $form = ActiveForm::begin(); ?>
		<p>主题标题 <span class="gray">( 如果标题能够表达完整内容，主题内容可为空 )</span></p>
	    <?php echo $form->field($model, 'title')->textArea(['rows' => '4', 'maxlength'=>120])->label(false); ?>
		<p>主题内容</p>
		<?php echo $form->field($content, 'content')->textArea(['id'=>'editor', 'maxlength'=>30000])->label(false); ?>
	    <?php echo $form->field($model, 'node_id')->dropDownList(array(''=>'')+Node::getNodeList(), ['class'=>'form-control nodes-select2'])->label(false); ?>
		<div class="new-hot-nodes">热门节点：
		<?php
			$hotNodes = Node::getHotNodes();
			foreach($hotNodes as $hn) {
				echo Html::a(Html::encode($hn['name']), 'javascript:chooseNode("'.$hn['id'].'");', ['class'=>'node']);
			}
		?>
		</div>
<?php
	if( Yii::$app->getUser()->getIdentity()->canUpload($settings) ) {
		$editor->registerUploadAsset($this);
		echo '<div class="form-group"><div id="fileuploader">图片上传</div></div>';
	}
?>
		<div class="form-group">
			<?php echo Html::submitButton('创建', ['class' => 'btn btn-primary']); ?>
		</div>
<?php ActiveForm::end(); ?>
	</div>
</div>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
