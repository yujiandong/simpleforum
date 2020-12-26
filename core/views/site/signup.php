<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\SignupForm;
use app\components\SfHtml;


if ($model->action === SignupForm::ACTION_AUTH_SIGNUP) {
    $this->title = Yii::t('app', 'Create an account and bind a third-party account');
    $btnLabel = Yii::t('app', 'Create an account and bind it');
} else {
    $this->title = Yii::t('app', 'Sign up');
    $btnLabel = $this->title;
}
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="panel-body sf-box-form">
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => 'form-signup'
        ]); ?>
<?php if ($model->action === SignupForm::ACTION_AUTH_SIGNUP) : ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <p class="form-control-static"><?php echo Yii::t('app', 'You have signed in with your {name} account.', ['name'=>$authInfo['sourceName']]); ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3"><?php echo Yii::t('app', 'Username'); ?></label>
            <div class="col-sm-6">
                <p class="form-control-static"><strong><?php echo $authInfo['username']; ?></strong></p>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3"><?php echo Yii::t('app', 'Have an account?'); ?></label>
            <div class="col-sm-6">
                <p class="form-control-static"><?php echo Html::a(Yii::t('app', 'Bind your account'), ['auth-bind-account'], ['class'=>'btn btn-primary']); ?></p>
            </div>
        </div>
        <br /><strong><?php echo Yii::t('app', 'Create an account'); ?></strong><hr>
<?php endif; ?>
            <?php echo $form->field($model, 'username')->textInput(['maxlength'=>16])->hint(Yii::t('app', 'letters, numbers and \'_\'')); ?>
            <?php echo $form->field($model, 'name')->textInput(['maxlength'=>40])->hint(Yii::t('app', 'display name')); ?>
            <?php echo $form->field($model, 'email')->textInput(['maxlength'=>50]); ?>
            <?php echo $form->field($model, 'password')->passwordInput(['maxlength'=>20]); ?>
            <?php echo $form->field($model, 'password_repeat')->passwordInput(['maxlength'=>20]); ?>
<?php
if ( intval(Yii::$app->params['settings']['close_register']) === 2 ) {
    echo $form->field($model, 'invite_code')->textInput(['maxlength'=>6]);
}
        $captcha = ArrayHelper::getValue(Yii::$app->params, 'settings.captcha', '');
        if(!empty($captcha) && ($plugin=ArrayHelper::getValue(Yii::$app->params, 'plugins.' . $captcha, []))) {
            $plugin['class']::captchaWidget('signup', $form, $model, null, $plugin);
        }
?>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                <?php echo Html::submitButton($btnLabel, ['class' => 'btn btn-primary', 'name' => 'signup-button']); ?>
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
