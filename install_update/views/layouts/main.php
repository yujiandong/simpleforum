<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
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
			<p>
				Powered by <a href="http://simpleforum.org/" rel="external">极简论坛 <?php echo SIMPLE_FORUM_VERSION; ?></a>
			</p>
        </div>
    </footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
