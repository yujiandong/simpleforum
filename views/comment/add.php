<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

$session = Yii::$app->getSession();
$settings = Yii::$app->params['settings'];
$me = Yii::$app->getUser()->getIdentity();

$editor = new \app\lib\Editor(['editor'=>$settings['editor']]);
$editor->registerAsset($this);

$this->title = '添加回复';
?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-body">
		<?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', Html::a(Html::encode($topic['node']['name']), ['topic/node', 'name'=>$topic['node']['ename']]); ?>
		<h3><?php echo Html::a(Html::encode($topic['title']), ['topic/view', 'id'=>$topic['id']]); ?></h3>
		<small class="gray">
		<?php echo 'by ', Html::a(Html::encode($topic['author']['username']), ['user/view', 'username'=>Html::encode($topic['author']['username'])]), 
			'  •  ', Yii::$app->getFormatter()->asRelativeTime($topic['created_at']); ?>
		</small>
	</div>
</div>

<div class="panel panel-default sf-box">
    <div class="panel-heading">添加回复</div>
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
<?php
    echo $form->field($comment, 'content')->textArea(['id'=>'editor', 'maxlength'=>30000])->label(false);
    if($me->canUpload($settings)) {
        $editor->registerUploadAsset($this);
        echo '<div class="form-group"><div id="fileuploader">图片上传</div></div>';
    }
?>
    <div class="form-group">
        <?php echo Html::submitButton('<i class="fa fa-reply"></i>回复', ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end(); ?> </div>
</div>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
