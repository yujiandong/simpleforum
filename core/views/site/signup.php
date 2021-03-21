<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\models\SignupForm;


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
<div class="col-lg-8 sf-left">

<div class="card sf-box">
    <div class="card-header sf-box-header sf-navi">
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="card-body sf-box-form">
<?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => 'form-signup',
            'fieldConfig' => [
              'horizontalCssClasses' => [
                  'label' => 'col-form-label col-sm-3 text-sm-right',
                  'wrapper' => 'col-sm-9',
              ],
            ],
        ]); ?>
<?php if ($model->action === SignupForm::ACTION_AUTH_SIGNUP) : ?>
        <div class="form-group row">
            <div class="offset-sm-3 col-sm-9">
                <?php echo Yii::t('app', 'You have signed in with your {name} account.', ['name'=>$authInfo['sourceName']]); ?>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-sm-3 text-sm-right"><?php echo Yii::t('app', 'Username'); ?></label>
            <div class="col-sm-9">
                <strong><?php echo $authInfo['username']; ?></strong>
            </div>
        </div>
        <div class="form-group row">
            <label class="control-label col-sm-3 text-sm-right"><?php echo Yii::t('app', 'Have an account?'); ?></label>
            <div class="col-sm-9">
                <?php echo Html::a(Yii::t('app', 'Bind your account'), ['auth-bind-account'], ['class'=>'btn sf-btn']); ?>
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
                <div class="offset-sm-3 col-sm-9">
                <?php echo Html::submitButton($btnLabel, ['class' => 'btn sf-btn', 'name' => 'signup-button']); ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
