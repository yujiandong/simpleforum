<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

//\app\assets\Select2Asset::register($this);
//$this->registerJs("$('select').select2();");

$this->title = '第三方帐号登录';

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
        <span class="fr">参考 <a href="http://simpleforum.org/t/2" target="_blank">开启第三方帐号登录</a></span>
        <?php
            echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
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
    echo '<div id=auth_',$key,' class="auth-item"><p class="login-three-home"><strong><span class="auth-item-id">', $type===1?'设定'.$type:$type.'登录设定', '</span> <span class="auth-item-del">删除</span></strong></p>';
    showSettingForm($part, $form, '['.$key.']');
    echo '</div>';
}
?>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?> <?php echo Html::button('添加', ['class' => 'btn btn-primary auth-item-add']); ?>
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
