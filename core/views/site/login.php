<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

$session = Yii::$app->getSession();

$this->title = '登录';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="panel-body sf-box-form">
<?php
if ( $session->hasFlash('accessNG') ) {
echo Alert::widget([
       'options' => ['class' => 'alert-warning'],
       'body' => $session->getFlash('accessNG'),
    ]);
}
?>
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => 'form-login'
        ]); ?>
        <?php echo $form->field($model, 'username')->textInput(['maxlength'=>20]); ?>
        <?php echo $form->field($model, 'password')->passwordInput(['maxlength'=>20]); ?>
        <?php echo $form->field($model, 'rememberMe')->checkbox(); ?>
<?php
if ( intval(Yii::$app->params['settings']['captcha_enabled']) === 1 ) {
    echo $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname());
}
?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('登录', ['class' => 'btn btn-primary', 'name' => 'login-button']); ?>
            &nbsp;&nbsp;<?php echo Html::a('忘记密码了', ['site/forgot-password']); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
<?php if ( intval(Yii::$app->params['settings']['auth_enabled']) === 1 ) : ?>
        <h6 class="login-three-home"><strong>第三方账号直接登录</strong></h6><div class="auth-client">
        <?php //echo
/*        \yii\authclient\widgets\AuthChoice::widget([
            'baseAuthUrl' => ['site/auth'],
            'popupMode' => false,
        ]);*/
foreach (Yii::$app->authClientCollection->getClients() as $client){
    if ($client->getId() == 'weixin' && $client->type == 'mp') {
        echo Html::a('<span class="auth-icon '.$client->getId().'"></span><span class="auth-title">'. Html::encode($client->getTitle()) . '</span>', 'javascript:void(0);', ['class'=>'auth-link '. $client->getId(), 'id'=>'weixinmp', 'link'=>Url::to(['site/auth', 'authclient'=>$client->getId()], true)]);
    } else {
        echo Html::a('<span class="auth-icon '.$client->getId().'"></span><span class="auth-title">'. Html::encode($client->getTitle()) . '</span>', ['site/auth', 'authclient'=>$client->getId()], ['class'=>'auth-link '. $client->getId(), 'title'=>Html::encode($client->getTitle())]);
    }
}
        ?></div>
<?php endif; ?>
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
