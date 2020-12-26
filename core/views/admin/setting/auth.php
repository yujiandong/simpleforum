<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

//\app\assets\Select2Asset::register($this);
//$this->registerJs("$('select').select2();");

$this->title = Yii::t('app', 'Third-party login');

function showSettingForm($settings, $form, $parentKey='')
{
    $key = 0;
    foreach ($settings as $setting):
        echo $form->field($setting, $parentKey.'['.$key.']key', ['enableError'=>false,])->hiddenInput(['class'=>'form-control auth-key'])->label(false);
        if ($setting->type === 'select') {
            $options = json_decode($setting->option,true);
            echo $form->field($setting, $parentKey.'['.$key.']value', ['enableError'=>false,])
                    ->dropDownList($options, ['class'=>'form-control auth-value auth-'.$setting->key])->label($setting->label)->hint($setting->description);
        } else if ($setting->type === 'textarea') {
            echo $form->field($setting, $parentKey.'['.$key.']value', ['enableError'=>false,])
                    ->textArea(['class'=>'form-control auth-value auth-'.$setting->key])->label($setting->label)->hint($setting->description);
        } else  {
            echo $form->field($setting, $parentKey.'['.$key.']value', ['enableError'=>false,])->input($setting->type, ['class'=>'form-control auth-value auth-'.$setting->key])
                    ->label($setting->label)->hint($setting->description);
        }
        $key++;
    endforeach;
}
?>
<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item">
        <?php
            echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
        ?>
    </li>
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'id' => 'form-setting',
    'fieldConfig' => [
//      'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-3',
//          'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-9',
            'error' => '',
            'hint' => 'col-sm-offset-3 col-sm-9',
        ],
    ],
]); ?>
    <li class="list-group-item">
        <div class="auth-items">
<?php
showSettingForm([$settings['auth_enabled']], $form, '[0]');
unset($settings['auth_enabled']);
$key = 0;
foreach($settings as $type=>$part) {
    $key++;
    echo '<div id=auth_',$key,' class="auth-item"><p class="login-three-home"><strong><span class="auth-item-id">', $type===1?'Account'.$type:$type, '</span> <span class="auth-item-del">' . Yii::t('app', 'Delete') . '</span></strong></p>';
    showSettingForm($part, $form, '['.$key.']');
    echo '</div>';
}
?>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']); ?> <?php echo Html::button(Yii::t('app', 'Add'), ['class' => 'btn btn-primary auth-item-add']); ?>
            </div>
        </div>
    </li>

<?php
ActiveForm::end();
?>
</ul>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->

</div>
