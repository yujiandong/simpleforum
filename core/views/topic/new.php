<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Alert;
use app\models\Node;
use app\models\Topic;

$session = Yii::$app->getSession();
$settings = Yii::$app->params['settings'];

$this->registerAssetBundle('app\assets\Select2Asset');
$this->registerJs('$(".nodes-select2").select2({placeholder:"请选择一个节点",allowClear: true});');

//$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);
$editorClass = '\app\plugins\\'. $settings['editor']. '\\'. ucfirst($settings['editor']);
$editor = new $editorClass();
$editor->registerAsset($this);
$editor->registerTagItAsset($this);

$this->title = '发表新主题';
?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="panel-body">
<?php
if ( $session->hasFlash('postNG') ) {
echo Alert::widget([
       'options' => ['class' => 'alert-warning'],
       'body' => $session->getFlash('postNG'),
    ]);
}
?>
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
		<p>查看权限</p>
	    <?php echo $form->field($model, 'access_auth')->dropDownList(Topic::$access)->label(false); ?>
        <p>标签 <span class="gray">( 最多4个，以空格分隔 )</span></p>
        <?php echo $form->field($model, 'tags')->textInput(['id'=>'tags', 'maxlength'=>60])->label(false); ?>
<?php
    if( Yii::$app->getUser()->getIdentity()->canUpload($settings) ) {
        $editor->registerUploadAsset($this);
        echo '<div class="form-group"><div id="fileuploader">图片上传</div></div>';
    }
?>
        <div class="form-group">
            <?php echo Html::submitButton('<i class="fa fa-pencil"></i>发表', ['class' => 'btn btn-primary']); ?>
        </div>
<?php ActiveForm::end(); ?>
    </div>
</div>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
