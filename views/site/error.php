<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\Alert;

$session = Yii::$app->getSession();

$this->title = '出错了';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">
	<div class="box">
		<div class="inner">
			<?= Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title ?>
		</div>
		<div class="cell">
    <p><strong><?= Html::encode($name) ?></strong></p>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        论坛处理您的请求时，发生了以上错误。
    </p>
    <p>
        如您认为是服务器错误或论坛程序错误，请联系站长 <?= Yii::$app->params['setting']['admin_email'] ?>
    </p>
		</div>
	</div>
</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?= $this->render('@app/views/common/_right') ?>
</div>
<!-- sf-right end -->

</div>
