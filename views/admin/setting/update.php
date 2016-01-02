<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

\app\assets\Select2Asset::register($this);
$this->registerJs("$('select').select2();");

$this->title = '论坛配置';

$blocks = [
	'info'=>['title'=>'论坛信息设置', 'msg'=>'', 'parts'=>null],
	'manage'=>['title'=>'论坛管理设置', 'msg'=>'', 'parts'=>null],
	'mailer'=>['title'=>'SMTP服务器设置', 'msg'=>'修改保存后，请 '. Html::a('测试邮件发送', ['admin/setting/test-email']), 'parts'=>null],
	'auth' => [
		'title'=>'第三方帐号登录',
		'msg'=>'可选，不用则留空，参考 <a href="http://simpleforum.org/t/2" target="_blank">开启第三方帐号登录</a>',
		'parts'=>[
			'qq'=>'QQ登录设置',
			'weibo'=>'微博登录设置',
		],
	],
	'cache'=>['title'=>'缓存设置', 'msg'=>'', 'parts'=>null],
	'upload'=>['title'=>'上传设置', 'msg'=>'', 'parts'=>null],
	'extend'=>['title'=>'扩展设置', 'msg'=>'', 'parts'=>null],
	'other'=>['title'=>'其它设置', 'msg'=>'下面一般保持默认', 'parts'=>null],
];


function showSettingForm($settings, $form)
{
	foreach ($settings as $id=>$setting):
		if ($setting->type === 'select') {
			if ($setting->key === 'timezone') {
				$options = \DateTimeZone::listIdentifiers();
				$options = array_combine($options,$options);
			} else {
				$options = json_decode($setting->option);
			}
			echo $form->field($setting, "[$id]value", ['enableError'=>false,])
					->dropDownList($options)->label($setting->label)->hint($setting->description);
		} else if ($setting->type === 'textarea') {
			echo $form->field($setting, "[$id]value", ['enableError'=>false,])
					->textArea()->label($setting->label)->hint($setting->description);
		} else  {
			echo $form->field($setting, "[$id]value", ['enableError'=>false,])
					->label($setting->label)->hint($setting->description);
		}
	endforeach;
}
?>
<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<?php
			echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
		?>
	</li>
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
	'id' => 'form-setting',
    'fieldConfig' => [
//		'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-3',
//			'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-9',
            'error' => '',
            'hint' => 'col-sm-offset-3 col-sm-9',
        ],
    ],
]); ?>
<?php
foreach ($blocks as $key=>$block):
?>
	<li class="list-group-item list-group-item-info" id="<?php echo $key; ?>">
		<div class="row">
    		<div class="text-right col-sm-3 col-xs-6"><strong>» <?php echo $block['title']; ?></strong></div>
        	<div class="col-sm-9 col-xs-12"><?php echo $block['msg']; ?></div>
		</div>
    </li>
	<li class="list-group-item sf-box-form">
<?php
if ( !empty($settings[$key]) ) {
	showSettingForm($settings[$key], $form);
}
if ( $block['parts'] ) {
	foreach($block['parts'] as $partKey=>$part) {
		echo '<p class="login-three-home"><strong>'.$part.'</strong></p>';
		showSettingForm($settings[$key.'.'.$partKey], $form);
	}
}
?>
        <div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary']); ?>
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
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->

</div>
