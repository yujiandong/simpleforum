<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\components\SfHtml;

$session = Yii::$app->getSession();

$this->title = Yii::t('app', 'Sign in');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="panel-body sf-box-form">
<?php
if ( $session->hasFlash('accessNG') ) {
echo Alert::widget([
       'options' => ['class' => 'alert-warning'],
       'body' => Yii::t('app', $session->getFlash('accessNG')),
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
        $captcha = ArrayHelper::getValue(Yii::$app->params, 'settings.captcha', '');
        if(!empty($captcha) && ($plugin=ArrayHelper::getValue(Yii::$app->params, 'plugins.' . $captcha, []))) {
            $plugin['class']::captchaWidget('signin', $form, $model, null, $plugin);
        }
        ?>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app', 'Sign in'), ['class' => 'btn btn-primary', 'name' => 'login-button', 'id'=>'recaptchaToken']); ?>
            &nbsp;&nbsp;<?php echo Html::a(Yii::t('app', 'Forgot password?'), ['site/forgot-password']); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
<?php if ( intval(Yii::$app->params['settings']['auth_enabled']) === 1 ) : ?>
        <h6 class="login-three-home"><strong><?php echo Yii::t('app', 'Third-party login'); ?></strong></h6><div class="auth-client">
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
