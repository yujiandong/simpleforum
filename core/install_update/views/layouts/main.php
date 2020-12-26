<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

\app\assets\AppAsset::register($this);

$settings = Yii::$app->params['settings'];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>">
<head>
    <meta charset="<?php echo Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <?php echo Html::csrfMetaTags(); ?>
    <?php echo $settings['head_meta']; ?>
    <title><?php echo Html::encode($this->title); ?></title>
    <?php $this->head() ?>
	<style>
		table {font-size:14px;}
	</style>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => $settings['site_name'],
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

            NavBar::end();
        ?>
        <div class="container">
            <?php echo $content; ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
<ul class="footer-links list-inline">
	<!-- language selector start -->
	<li class="dropup">
	        <a id="drop4" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
	          Language<span class="caret"></span>
	        </a>
	        <ul id="menu1" class="dropdown-menu" aria-labelledby="drop4">
	          <li><a href="/site/language?language=en-US">English</a></li>
	          <li><a href="/site/language?language=ja">日本語</a></li>
	          <li><a href="/site/language?language=zh-CN">简体中文</a></li>
	        </ul>
	</li>
	<!-- language selector end -->
</ul>
<div class="copyright">
<span>Powered by <a href="http://simpleforum.org/" rel="external" target="_blank">SimpleForum <?php echo SIMPLE_FORUM_VERSION; ?></a></span>
</div>
        </div>
    </footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
