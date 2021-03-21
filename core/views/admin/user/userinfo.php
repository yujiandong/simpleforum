<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Alert;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Topic */

$this->title = Yii::t('app/admin', 'Member\'s information');
$session = Yii::$app->getSession();
$formatter = Yii::$app->getFormatter();
?>

<div class="row">
<!-- sf-left start -->
<div class="col-lg-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item sf-box-header sf-navi">
        <?php echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title; ?>
    </li>
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'id' => 'form-setting',
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-form-label col-sm-3 text-sm-right',
            'wrapper' => 'col-sm-9',
        ],
    ],
]); ?>
    <li class="list-group-item">
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-sm-right"><?php echo Yii::t('app', 'Username'); ?></label>
            <div class="col-sm-9">
                <strong class="form-control bg-light"><?php echo $user->username; ?></strong>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-sm-right"><?php echo Yii::t('app/admin', 'Register time'); ?></label>
            <div class="col-sm-9">
                <strong class="form-control bg-light"><?php echo $formatter->asDateTime($user->getUser()->created_at, 'y-MM-dd HH:mm:ssZ'); ?></strong>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-sm-right"><?php echo Yii::t('app/admin', 'Register IP'); ?></label>
            <div class="col-sm-9">
                 <strong class="form-control bg-light"><?php echo long2ip($user->getUser()->userInfo->reg_ip); ?></strong>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-sm-right"><?php echo Yii::t('app/admin', 'Last login time'); ?></label>
            <div class="col-sm-9">
                 <strong class="form-control bg-light"><?php echo $formatter->asDateTime($user->getUser()->userInfo->last_login_at, 'y-MM-dd HH:mm:ssZ'); ?></strong>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-sm-right"><?php echo Yii::t('app/admin', 'Last login IP'); ?></label>
            <div class="col-sm-9">
                 <strong class="form-control bg-light"><?php echo long2ip($user->getUser()->userInfo->last_login_ip); ?></strong>
            </div>
        </div>
    </li>
    <li class="list-group-item list-group-item-info"><strong><?php echo Yii::t('app', 'Edit {attribute}', ['attribute'=>Yii::t('app/admin', 'Member\'s information')]); ?></strong></li>
    <li class="list-group-item sf-box-form">
<?php
if ( $session->hasFlash('adminProfileNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => Yii::t('app', $session->getFlash('adminProfileNG')),
        ]);
} else if ( $session->hasFlash('adminProfileOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => Yii::t('app', $session->getFlash('adminProfileOK')),
        ]);
}
?>
        <?php echo $form->field($user, "status")->dropDownList(User::getStatusList()); ?>
        <?php echo $form->field($user, "name")->textInput(['maxlength'=>40]); ?>
        <?php echo $form->field($user, "email")->textInput(['maxlength'=>50]); ?>
        <div class="form-group">
            <div class="offset-sm-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app', 'Edit'), ['class' => 'btn sf-btn']); ?>
            </div>
        </div>
    </li>
<?php
    ActiveForm::end();
?>
    <li class="list-group-item list-group-item-info"><strong><?php echo Yii::t('app', 'Edit {attribute}', ['attribute'=>Yii::t('app', 'Password')]); ?></strong></li>
    <li class="list-group-item sf-box-form">
<?php
if ( $session->hasFlash('adminPwdNG') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-warning'],
           'body' => Yii::t('app', $session->getFlash('adminPwdNG')),
        ]);
} else if ( $session->hasFlash('adminPwdOK') ) {
    echo Alert::widget([
           'options' => ['class' => 'alert-success'],
           'body' => Yii::t('app', $session->getFlash('adminPwdOK')),
        ]);
}
?>
    <?php $form = ActiveForm::begin([
        'action' => ['admin/user/reset-password', 'id'=>$user->id],
        'layout' => 'horizontal',
        'id' => 'form-setting',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'label' => 'col-form-label col-sm-3 text-md-right',
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]); ?>
        <?php echo $form->field($user, "password")->textInput(['maxlength'=>20]); ?>
        <div class="form-group">
        <div class="offset-sm-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app', 'Edit'), ['class' => 'btn sf-btn']); ?>
        </div>
        </div>
    <?php
        ActiveForm::end();
    ?>
    </li>
</ul>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->

</div>
