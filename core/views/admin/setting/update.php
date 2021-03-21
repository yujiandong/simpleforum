<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\ActiveForm;

\app\assets\Select2Asset::register($this);
$this->registerJs("$('select').select2();");

$this->title = Yii::t('app/admin', 'Settings');

$blocks = [
    'info'=>['title'=>Yii::t('app/admin', 'Forum info'), 'msg'=>'', 'parts'=>null],
    'manage'=>['title'=>Yii::t('app/admin', 'Forum manage'), 'msg'=>'', 'parts'=>null],
    'mailer'=>['title'=>Yii::t('app/admin', 'SMTP server'), 'msg'=>Yii::t('app/admin', 'Please {action} after saving SMTP server settings.', ['action'=>Html::a(Yii::t('app/admin', 'send a test email'), ['admin/setting/test-email'])]), 'parts'=>null],
    'cache'=>['title'=>Yii::t('app/admin', 'Cache'), 'msg'=>'', 'parts'=>null],
    'upload'=>['title'=>Yii::t('app/admin', 'Upload'), 'msg'=>'', 'parts'=>null],
    'extend'=>['title'=>Yii::t('app/admin', 'Extend'), 'msg'=>'', 'parts'=>null],
    'other'=>['title'=>Yii::t('app/admin', 'Others'), 'msg'=>Yii::t('app/admin', 'Default settings'), 'parts'=>null],
];

function showSettingForm($settings, $form, $languages)
{
    ArrayHelper::multisort($settings, ['sortid', 'id'], [SORT_ASC, SORT_ASC]);
    foreach ($settings as $setting):
        if ($setting->type === 'select') {
            if ($setting->key === 'timezone') {
                $options = \DateTimeZone::listIdentifiers();
                $options = array_combine($options,$options);
            } else if ($setting->key === 'language') {
                $options = $languages;
                $options = array_combine($options,$options);
            } else {
                $options = json_decode($setting->option,true);
                foreach ($options as $k=>$v) {
                  $options[$k] = Yii::t('app/admin', $v);
                }
            }
            echo $form->field($setting, "[$setting->id]value", ['enableError'=>false,])
                    ->dropDownList($options)->label(Yii::t('app/admin', $setting->label))->hint(Yii::t('app/admin', $setting->description));
        } else if ($setting->type === 'textarea') {
            echo $form->field($setting, "[$setting->id]value", ['enableError'=>false,])
                    ->textArea()->label(Yii::t('app/admin', $setting->label))->hint(Yii::t('app/admin', $setting->description));
        } else  {
            echo $form->field($setting, "[$setting->id]value", ['enableError'=>false,])->input($setting->type, $setting->type==='password'?['autocomplete'=>'new-password']:[])
                    ->label(Yii::t('app/admin', $setting->label))->hint(Yii::t('app/admin', $setting->description));
        }
    endforeach;
}
?>
<div class="row">
<!-- sf-left start -->
<div class="col-lg-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item sf-box-header sf-navi">
        <?php
            echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
        ?>
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
<?php
foreach ($blocks as $key=>$block):
?>
    <li class="list-group-item list-group-item-info" id="<?php echo $key; ?>">
        <div class="row">
            <div class="col-5 col-sm-4"><strong>» <?php echo $block['title']; ?></strong></div>
            <div class="col-7 col-sm-8"><?php echo $block['msg']; ?></div>
        </div>
    </li>
    <li class="list-group-item sf-box-form">
<?php
if ( !empty($settings[$key]) ) {
    showSettingForm($settings[$key], $form, $languages);
}
if ( $block['parts'] ) {
    foreach($block['parts'] as $partKey=>$part) {
        echo '<p class="login-three-home"><strong>'.$part.'</strong></p>';
        showSettingForm($settings[$key.'.'.$partKey], $form, $languages);
    }
}
?>
        <div class="form-group">
            <div class="offset-sm-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn sf-btn']); ?>
            </div>
        </div>
    </li>
<?php endforeach; ?>

<?php
ActiveForm::end();
?>
</ul>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->

</div>
