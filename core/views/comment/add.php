<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\components\SfHtml;

$session = Yii::$app->getSession();
$settings = Yii::$app->params['settings'];
$me = Yii::$app->getUser()->getIdentity();

//$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);
$editorClass = '\app\plugins\\'. $settings['editor']. '\\'. ucfirst($settings['editor']);
$editor = new $editorClass();
$editor->registerAsset($this);

$this->title = Yii::t('app', 'Add Comment');
?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-body">
		<?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']]); ?>
		<h3><?php echo Html::a(Html::encode($topic['title']), ['topic/view', 'id'=>$topic['id']]); ?></h3>
		<small class="gray">
		<?php echo 'by ', SfHtml::uLink($topic['author']['username'], $topic['author']['name']), 
			'  •  ', Yii::$app->getFormatter()->asRelativeTime($topic['created_at']); ?>
		</small>
	</div>
</div>

<div class="panel panel-default sf-box">
    <div class="panel-heading"><?php echo $this->title; ?></div>
    <div class="panel-body">
<?php
if ( $session->hasFlash('postNG') ) {
echo Alert::widget([
       'options' => ['class' => 'alert-warning'],
       'body' => Yii::t('app', $session->getFlash('postNG')),
    ]);
}
?>
<?php $form = ActiveForm::begin(); ?>
<?php
    echo $form->field($comment, 'content')->textArea(['id'=>'editor', 'maxlength'=>20000])->label(false);
    if($me->canUpload($settings)) {
        $editor->registerUploadAsset($this);
        echo '<div class="form-group"><div id="fileuploader">', Yii::t('app', 'Upload Images'), '</div></div>';
    }
?>
<?php
        $captcha = ArrayHelper::getValue(Yii::$app->params, 'settings.captcha', '');
        if(!empty($captcha) && ($plugin=ArrayHelper::getValue(Yii::$app->params, 'plugins.' . $captcha, []))) {
            $plugin['class']::captchaWidget('addcomment', $form, $comment, null, $plugin);
        }
?>
    <div class="form-group">
        <?php echo Html::submitButton('<i class="fa fa-reply"></i>' . Yii::t('app', 'Comment'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end(); ?> </div>
</div>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
