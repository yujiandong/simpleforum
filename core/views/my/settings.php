<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\authclient\Collection;
use app\models\UserInfo;
use app\models\UploadForm;
use app\models\ChangePasswordForm;
use app\models\ChangeEmailForm;
use app\models\Auth;

$this->title = '会员设置';
$session = Yii::$app->getSession();
$me = Yii::$app->getUser()->getIdentity();
$cpModel = new ChangePasswordForm();
$ceModel = new ChangeEmailForm();

?>

<div class="row">
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item">
        <?php echo Html::a('首页', ['topic/index']), '&nbsp;›&nbsp;', $this->title; ?>
    </li>
<?php if ($me->isWatingActivation()) : ?>
    <li class="list-group-item list-group-item-info" id="activate"><strong>会员激活</strong></li>
    <li class="list-group-item">
<?php
if ( $session->hasFlash('activateMailNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => $session->getFlash('activateMailNG'),
        ]);
} else if ( $session->hasFlash('activateMailOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => $session->getFlash('activateMailOK'),
        ]);
}
?>
        您还有没有激活，请进您注册时填写的邮箱(<?php echo $me->email; ?>)，点击激活链接。<br />
        <?php echo Html::a('重发激活邮件', ['service/send-activate-mail']); ?> | <a href="#password">修改邮箱</a>
    </li>
<?php endif; ?>
    <li class="list-group-item list-group-item-info" id="info"><strong>修改信息</strong></li>
    <li class="list-group-item">
<?php
if ( $session->hasFlash('EditProfileNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => $session->getFlash('EditProfileNG'),
        ]);
} else if ( $session->hasFlash('EditProfileOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => $session->getFlash('EditProfileOK'),
        ]);
}
?>
<?php $form = ActiveForm::begin([
        'action' => ['service/edit-profile'],
        'layout' => 'horizontal',
        ]); ?>
        <div class="form-group">
            <label class="control-label col-sm-3 username-label">用户名</label>
            <div class="col-sm-6" style="padding-top:7px;">
                <strong><?php echo $me->username; ?></strong>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3 status-label">状态</label>
            <div class="col-sm-6" style="padding-top:7px;">
                <?php echo $me->getStatus(); ?>
            </div>
        </div>
        <?php echo $form->field($me->userInfo, 'website')->textInput(['maxlength'=>100]); ?>
        <?php echo $form->field($me->userInfo, 'about')->textArea(['maxlength'=>255]); ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
<?php ActiveForm::end(); ?>
    </li>
    <li class="list-group-item list-group-item-info" id="avatar"><strong>修改头像</strong></li>
    <li class="list-group-item">
<?php
if ( $session->hasFlash('setAvatarNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => $session->getFlash('setAvatarNG'),
        ]);
} else if ( $session->hasFlash('setAvatarOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => $session->getFlash('setAvatarOK'),
        ]);
}
?>
<?php $form = ActiveForm::begin([
        'action' => ['service/avatar'],
        'options' => ['enctype' => 'multipart/form-data'],
        'layout' => 'horizontal',
        ]); ?>
        <div class="form-group">
            <label class="control-label col-sm-3 avatar-label">当前头像</label>
            <div class="col-sm-6">
            <?php echo Html::img('@web/'.str_replace('{size}', 'large', $me->avatar), ['class'=>'img-circle']), ' ', Html::img('@web/'.str_replace('{size}', 'normal', $me->avatar), ['class'=>'img-circle']), ' ', Html::img('@web/'.str_replace('{size}', 'small', $me->avatar), ['class'=>'img-circle']); ?>
            </div>
        </div>
        <?php echo $form->field((new UploadForm()), 'file')->fileInput(); ?>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('上传', ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
<?php ActiveForm::end(); ?>
    </li>
    <li class="list-group-item list-group-item-info" id="email"><strong>修改邮箱</strong></li>
    <li class="list-group-item">
<?php
if ( $session->hasFlash('chgEmailNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => $session->getFlash('chgEmailNG'),
        ]);
} else if ( $session->hasFlash('chgEmailOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => $session->getFlash('chgEmailOK'),
        ]);
}
?>
<?php $form = ActiveForm::begin([
        'action' => ['service/change-email'],
        'layout' => 'horizontal',
        ]);
?>
        <div class="form-group">
            <label class="control-label col-sm-3 username-label">当前邮箱</label>
            <div class="col-sm-6" style="padding-top:7px;">
                <strong><?php echo $me->email; ?></strong>
            </div>
        </div>
        <?php echo $form->field($ceModel, 'email')->textInput(['maxlength'=>50]); ?>
        <?php echo $form->field($ceModel, 'password')->passwordInput(['maxlength'=>20]); ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
<?php ActiveForm::end(); ?>
    </li>
    <li class="list-group-item list-group-item-info" id="password"><strong>修改密码</strong></li>
    <li class="list-group-item sf-box-form">
<?php
if ( $session->hasFlash('chgPwdNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => $session->getFlash('chgPwdNG'),
        ]);
} else if ( $session->hasFlash('chgPwdOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => $session->getFlash('chgPwdOK'),
        ]);
}
?>
<?php $form = ActiveForm::begin([
        'action' => ['service/change-password'],
        'layout' => 'horizontal',
        ]);
?>
        <?php echo $form->field($cpModel, 'old_password')->passwordInput(['maxlength'=>20]); ?>
        <?php echo $form->field($cpModel, 'password')->passwordInput(['maxlength'=>20]); ?>
        <?php echo $form->field($cpModel, 'password_repeat')->passwordInput(['maxlength'=>20]); ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
<?php ActiveForm::end(); ?>
    </li>
<?php
if ( intval(Yii::$app->params['settings']['captcha_enabled']) === 1 ) :
?>
    <li class="list-group-item list-group-item-info" id="auth"><strong>第三方帐号登录</strong></li>
    <li class="list-group-item sf-box-form">
<?php
    $auths = ArrayHelper::getColumn($me->auths, 'source');
$authed = $unauthed = [];
foreach (Yii::$app->authClientCollection->getClients() as $client){
    if( in_array($client->getId(), $auths) ) {
        $authed[] = Html::a('<span class="auth-icon '.$client->getId().'"></span><span class="auth-title">'. Html::encode($client->getTitle()) . '</span>', ['service/unbind-account', 'source'=>$client->getId()], ['class'=>'auth-link '. $client->getId(), 'title'=>'解绑']);
    } else {
        $unauthed[] = Html::a('<span class="auth-icon '.$client->getId().'"></span><span class="auth-title">'. Html::encode($client->getTitle()) . '</span>', ['site/auth', 'authclient'=>$client->getId(), 'action'=>'bind'], ['class'=>'auth-link '. $client->getId(), 'title'=>'绑定']);
    }
}
?>
        <div class="row">
        <div class="col-sm-3 user-auth-label"><strong>已绑定</strong></div>
        <div class="col-sm-9 auth-client">&nbsp;<?php echo implode('', $authed); ?></div>
        </div>
        <div class="row" style="padding-top:7px;">
        <div class="col-sm-3 user-auth-label"><strong>未绑定</strong></div>
        <div class="col-sm-9 auth-client"><?php echo implode('', $unauthed); ?>
        </div>
    </li>
<?php endif; ?>
</ul>

</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
