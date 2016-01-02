<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
	<link rel="stylesheet" type="text/css" media="screen"  href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script>
    $(document).on("mobileinit", function(){
        $.mobile.ajaxEnabled = false;
        $.mobile.loadingMessage = false;
    });            
    </script>
	<script type="text/javascript" src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
<div data-role="page" class="sf">
        <div data-role="header" class="sf-header">
			<h1>Simple Forum</h1>
			<p><a href="/">首页</a><a href="/">注册</a><a href="/">登录</a></p>
        </div>
        <div role="main" class="ui-content">
			<div class="sf-content">
            <?= $content ?>
			</div>
        </div>
        <div data-role="footer" id="sf-footer">
<?php
//echo $this->element('m-footer');
?>
        </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
