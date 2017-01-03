<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\components\SfHtml;

$formatter = Yii::$app->getFormatter();
$session = Yii::$app->getSession();
if( $sms ) {
    $this->title = '回复私信';
} else {
    $this->title = '发送私信';
}
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="panel-body sf-box-form">
<?php if( $sms ): ?>
<dl class="well">
  <dt>消息</dt>
  <dd><p><?php echo $sms->msg; ?></p></dd>
  <dt>发件人</dt>
  <dd><p><?php echo SfHtml::uLink($sms['source']['username']); ?></p></dd>
  <dt>时间</dt>
  <dd><?php echo $formatter->asRelativeTime($sms['created_at']); ?></dd>
</dl>
<?php endif; ?>
<?php
if ( $session->hasFlash('SendMsgNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => $session->getFlash('SendMsgNG'),
        ]);
} else if ( $session->hasFlash('SendMsgOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => $session->getFlash('SendMsgOK'),
        ]);
}
?>
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => 'form-sms'
        ]); ?>
            <?php echo $form->field($model, 'username')->textInput(['maxlength'=>16]); ?>
            <?php echo $form->field($model, 'msg')->textarea(['rows' => 3, 'maxlength'=>255]); ?>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                <?php echo Html::submitButton('确定', ['class' => 'btn btn-primary']); ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
