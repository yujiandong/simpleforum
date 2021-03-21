<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\models\User;

$this->title = Yii::t('app/admin', 'Members');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-lg-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item sf-box-header sf-navi">
        <?php echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title; ?>
    </li>
    <li class="list-group-item list-group-item-info"><strong><?php echo Yii::t('app', 'Search'); ?></strong></li>
    <li class="list-group-item sf-box-form">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'id' => 'form-setting',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'label' => 'col-form-label col-sm-2 text-sm-right',
                'wrapper' => 'col-sm-10',
            ],
        ],
    ]); ?>
        <?php echo $form->field($model, "username"); ?>
        <div class="form-group">
            <div class="offset-sm-2 col-sm-10">
            <?php echo Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn sf-btn']); ?>
            </div>
        </div>
    <?php
        ActiveForm::end();
    ?>
    </li>
    <li class="list-group-item list-group-item-info">
        <span class="fr"><?php
                foreach(User::getStatusList() as $status=>$statusName) {
                    $links[] = Html::a($statusName, ['index', 'status'=>$status]);
                }
                echo implode('&nbsp;|&nbsp;', $links);
        ?></span><strong><?php echo Yii::t('app', 'Search result'); ?></strong>
    </li>
    <li class="list-group-item">
        <ul>
        <?php
            if( !empty($user) ) {
                echo '<li>[', $user['id'], '] ', Html::a($user['username'], ['info', 'id'=>$user['id']]), ($user['status']===User::STATUS_ACTIVE?'':'&nbsp;|&nbsp;'.Html::a(Yii::t('app/admin', 'Activate'), ['activate', 'id'=>$user['id']])), '</li>';
            } else {
                echo Yii::t('app', '{attribute} doesn\'t exist.', ['attribute'=>Yii::t('app', 'Username')]);
            }
        ?>
        </ul>
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
