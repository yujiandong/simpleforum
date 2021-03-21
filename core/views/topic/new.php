<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap4\Alert;
use app\models\Node;
use app\models\Topic;

$session = Yii::$app->getSession();
$settings = Yii::$app->params['settings'];

$this->registerAssetBundle('app\assets\Select2Asset');
$this->registerJs('$(".nodes-select2").select2({placeholder:"'. Yii::t('app', 'Please select a node').'",allowClear: true});');

//$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);
$editorClass = '\app\plugins\\'. $settings['editor']. '\\'. ucfirst($settings['editor']);
$editor = new $editorClass();
$editor->registerAsset($this);
$editor->registerTagItAsset($this);

$this->title = Yii::t('app', 'Add Topic');
?>

<div class="row">
<div class="col-lg-8 sf-left">

<div class="card sf-box">
    <div class="card-header sf-box-header sf-navi">
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="card-body">
<?php
if ( $session->hasFlash('postNG') ) {
echo Alert::widget([
       'options' => ['class' => 'alert-warning'],
       'body' => Yii::t('app', $session->getFlash('postNG')),
    ]);
}
?>
<?php $form = ActiveForm::begin(); ?>
        <p><?php echo Yii::t('app', 'Topic Title'); ?></p>
        <?php echo $form->field($model, 'title')->textArea(['rows' => '4', 'maxlength'=>120])->label(false); ?>
        <p><?php echo Yii::t('app', 'Topic Content'); ?> <span class="gray">( <?php echo Yii::t('app', 'Topic content can be empty.'); ?> )</span></p>
        <?php echo $form->field($content, 'content')->textArea(['id'=>'editor', 'maxlength'=>30000])->label(false); ?>
        <?php echo $form->field($model, 'node_id')->dropDownList(array(''=>'')+Node::getNodeList(), ['class'=>'form-control nodes-select2'])->label(false); ?>
        <div class="new-hot-nodes"><?php echo Yii::t('app', 'Hot Nodes'); ?>：
        <?php
            $hotNodes = Node::getHotNodes();
            foreach($hotNodes as $hn) {
                echo Html::a(Html::encode($hn['name']), 'javascript:chooseNode("'.$hn['id'].'");', ['class'=>'node']);
            }
        ?>
        </div>
		<p><?php echo Yii::t('app', 'Visible Option'); ?></p>
	    <?php echo $form->field($model, 'access_auth')->dropDownList(Topic::accessList())->label(false); ?>
        <p><?php echo Yii::t('app', 'Tags'); ?> <span class="gray">( <?php echo Yii::t('app', 'max: 4 tags, delimiter: blank'); ?> )</span></p>
        <?php echo $form->field($model, 'tags')->textInput(['id'=>'tags', 'maxlength'=>60])->label(false); ?>
<?php
    if( Yii::$app->getUser()->getIdentity()->canUpload($settings) ) {
        $editor->registerUploadAsset($this);
        echo '<div class="form-group"><div id="fileuploader">'.Yii::t('app', 'Upload Images').'</div></div>';
    }
?>
<?php
        $captcha = ArrayHelper::getValue(Yii::$app->params, 'settings.captcha', '');
        if(!empty($captcha) && ($plugin=ArrayHelper::getValue(Yii::$app->params, 'plugins.' . $captcha, []))) {
            $plugin['class']::captchaWidget('newtopic', $form, $model, null, $plugin);
        }
?>
        <div class="form-group">
            <?php echo Html::submitButton('<i class="fas fa-pencil-alt"></i>'.Yii::t('app', 'Post'), ['class' => 'btn sf-btn']); ?>
        </div>
<?php ActiveForm::end(); ?>
    </div>
</div>

</div>

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
