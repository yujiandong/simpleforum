<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Alert;
use yii\authclient\Collection;
use app\components\SfHtml;
use app\models\UploadForm;
use app\models\ChangePasswordForm;
use app\models\ChangeEmailForm;
use app\models\EditProfileForm;

$this->title = Yii::t('app', 'Account Settings');
$session = Yii::$app->getSession();
$me = Yii::$app->getUser()->getIdentity();
$epModel = new EditProfileForm();
$cpModel = new ChangePasswordForm();
$ceModel = new ChangeEmailForm();

?>

<div class="row">
<div class="col-lg-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item sf-box-header sf-navi">
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;›&nbsp;', $this->title; ?>
    </li>
<?php if ($me->isWatingActivation()) : ?>
    <li class="list-group-item list-group-item-info" id="activate"><strong><?php echo Yii::t('app', 'Account Activation'); ?></strong></li>
    <li class="list-group-item">
<?php
if ( $session->hasFlash('activateMailNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => Yii::t('app', $session->getFlash('activateMailNG')),
        ]);
} else if ( $session->hasFlash('activateMailOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => Yii::t('app', $session->getFlash('activateMailOK')),
        ]);
}
?>
        <?php echo Yii::t('app', 'Your account is not activated. Please check your email({email}) to activate your account.', ['email'=>$me->email]); ?><br />
        <?php echo Html::a(Yii::t('app', 'Resend activation email'), ['service/send-activate-mail']); ?> | <a href="#password"><?php echo Yii::t('app', 'Change {attribute}', ['attribute'=>Yii::t('app', 'Email')]); ?></a>
    </li>
<?php endif; ?>
    <li class="list-group-item list-group-item-info" id="info"><strong><?php echo Yii::t('app', 'Change {attribute}', ['attribute'=>Yii::t('app', 'Profile')]); ?></strong></li>
    <li class="list-group-item">
<?php
if ( $session->hasFlash('editProfileNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => $session->getFlash('editProfileNG'),
        ]);
} else if ( $session->hasFlash('editProfileOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => $session->getFlash('editProfileOK'),
        ]);
}
?>
<?php $form = ActiveForm::begin([
        'action' => ['service/edit-profile'],
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'label' => 'col-form-label col-sm-3 text-sm-right',
                'wrapper' => 'col-sm-9',
            ],
        ],
      ]); ?>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-sm-right"><?php echo Yii::t('app', 'Username'); ?></label>
            <div class="col-sm-9">
                <strong class="form-control bg-light"><?php echo $me->username; ?></strong>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-sm-right"><?php echo Yii::t('app', 'Status'); ?></label>
            <div class="col-sm-9">
                <strong class="form-control bg-light"><?php echo $me->getStatus(); ?></strong>
            </div>
        </div>
        <?php echo $form->field($epModel, 'name')->textInput(['maxlength'=>40, 'value'=>$me->name]); ?>
        <?php echo $form->field($epModel, 'website')->textInput(['maxlength'=>100, 'value'=>$me->userInfo->website]); ?>
        <?php echo $form->field($epModel, 'about')->textArea(['maxlength'=>255, 'value'=>$me->userInfo->about]); ?>
        <div class="form-group">
            <div class="offset-sm-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn sf-btn']); ?>
            </div>
        </div>
<?php ActiveForm::end(); ?>
    </li>
    <li class="list-group-item list-group-item-info" id="avatar"><strong><?php echo Yii::t('app', 'Change {attribute}', ['attribute'=>Yii::t('app', 'Avatar')]); ?></strong></li>
    <li class="list-group-item">
<?php
if ( $session->hasFlash('setAvatarNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => Yii::t('app', $session->getFlash('setAvatarNG')),
        ]);
} else if ( $session->hasFlash('setAvatarOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => Yii::t('app', $session->getFlash('setAvatarOK')),
        ]);
}
?>
<?php $form = ActiveForm::begin([
        'action' => ['service/avatar'],
        'options' => ['enctype' => 'multipart/form-data'],
        'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-form-label col-sm-3 text-sm-right',
                    'wrapper' => 'col-sm-9',
                ],
            ],
        ]); ?>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-sm-right py-4"><?php echo Yii::t('app', 'Current avatar'); ?></label>
            <div class="col-sm-9">
            <?php echo SfHtml::uimg($me, 'large', []), ' ', SfHtml::uImg($me, 'normal', []), ' ', SfHtml::uImg($me, 'small', []); ?>
            </div>
        </div>
        <?php echo $form->field((new UploadForm(Yii::$container->get('avatarUploader'))), 'file')->fileInput(); ?>

        <div class="form-group">
            <div class="offset-sm-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app', 'Upload'), ['class' => 'btn sf-btn']); ?>
            </div>
        </div>
<?php ActiveForm::end(); ?>
    </li>
    <li class="list-group-item list-group-item-info" id="email"><strong><?php echo Yii::t('app', 'Change {attribute}', ['attribute'=>Yii::t('app', 'Email')]); ?></strong></li>
    <li class="list-group-item">
<?php
if ( $session->hasFlash('chgEmailNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => Yii::t('app', $session->getFlash('chgEmailNG')),
        ]);
} else if ( $session->hasFlash('chgEmailOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => Yii::t('app', $session->getFlash('chgEmailOK')),
        ]);
}
?>
<?php $form = ActiveForm::begin([
        'action' => ['service/change-email'],
        'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-form-label col-sm-3 text-sm-right',
                    'wrapper' => 'col-sm-9',
                ],
            ],
        ]);
?>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-sm-right"><?php echo Yii::t('app', 'Current email'); ?></label>
            <div class="col-sm-9">
                <strong class="form-control bg-light"><?php echo $me->email; ?></strong>
            </div>
        </div>
        <?php echo $form->field($ceModel, 'email')->textInput(['maxlength'=>50]); ?>
        <?php echo $form->field($ceModel, 'password')->passwordInput(['maxlength'=>20, 'autocomplete'=>'new-password']); ?>
        <div class="form-group">
            <div class="offset-sm-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn sf-btn']); ?>
            </div>
        </div>
<?php ActiveForm::end(); ?>
    </li>
    <li class="list-group-item list-group-item-info" id="password"><strong><?php echo Yii::t('app', 'Change {attribute}', ['attribute'=>Yii::t('app', 'Password')]); ?></strong></li>
    <li class="list-group-item sf-box-form">
<?php
if ( $session->hasFlash('chgPwdNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => Yii::t('app', $session->getFlash('chgPwdNG')),
        ]);
} else if ( $session->hasFlash('chgPwdOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => Yii::t('app', $session->getFlash('chgPwdOK')),
        ]);
}
?>
<?php $form = ActiveForm::begin([
        'action' => ['service/change-password'],
        'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-form-label col-sm-3 text-sm-right',
                    'wrapper' => 'col-sm-9',
                ],
            ],
        ]);
?>
        <?php echo $form->field($cpModel, 'old_password')->passwordInput(['maxlength'=>20]); ?>
        <?php echo $form->field($cpModel, 'password')->passwordInput(['maxlength'=>20]); ?>
        <?php echo $form->field($cpModel, 'password_repeat')->passwordInput(['maxlength'=>20]); ?>
        <div class="form-group">
            <div class="offset-sm-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn sf-btn']); ?>
            </div>
        </div>
<?php ActiveForm::end(); ?>
    </li>
<?php
if ( intval(Yii::$app->params['settings']['auth_enabled']) === 1 ) :
?>
    <li class="list-group-item list-group-item-info" id="auth"><strong><?php echo Yii::t('app', 'Third-party login'); ?></strong></li>
    <li class="list-group-item sf-box-form">
<?php
    $auths = ArrayHelper::getColumn($me->auths, 'source');
$authed = $unauthed = [];
foreach (Yii::$app->authClientCollection->getClients() as $client){
    if( in_array($client->getId(), $auths) ) {
        $authed[] = '<div class="col ' .$client->getId() . '">' . Html::a('<i class="fab fa-'.$client->getId().'"></i><span class="snstext">'. Html::encode($client->getTitle()) . '</span>', ['service/unbind-account', 'source'=>$client->getId()], ['class'=>'btn auth-link '. $client->getId(), 'title'=>Yii::t('app', 'Unbind')]) . '</div>';
    } else {
        $unauthed[] = '<div class="col ' .$client->getId() . '">' . Html::a('<i class="fab fa-'.$client->getId().'"></i><span class="snstext">'. Html::encode($client->getTitle()) . '</span>', ['site/auth', 'authclient'=>$client->getId(), 'action'=>'bind'], ['class'=>'btn auth-link '. $client->getId(), 'title'=>Yii::t('app', 'Bind')]) . '</div>';
    }
}
?>
        <div class="clearfix">
          <h6 class="third-party-login-msg"><strong><?php echo Yii::t('app', 'Bound account'); ?></strong></h6>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 third-party-login">
              <?php echo implode('', $authed); ?>
          </div>
        </div>
        <div class="clearfix">
          <h6 class="third-party-login-msg"><strong><?php echo Yii::t('app', 'Bind account'); ?></strong></h6>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 third-party-login">
              <?php echo implode('', $unauthed); ?>
          </div>
        </div>
    </li>
<?php endif; ?>
</ul>

</div>

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
