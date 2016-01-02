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
$isGuest = Yii::$app->getUser()->getIsGuest();
if( !$isGuest ) {
	$me = Yii::$app->getUser()->getIdentity();
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <?= Html::csrfMetaTags() ?>
    <?= $settings['head_meta'] ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
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

			if ($isGuest) {
				$items = [
	                    ['label' => '首页', 'url' => ['topic/index']],
	                    ['label' => '登录', 'url' => ['/site/login']],
	                    ['label' => '注册', 'url' => ['/site/signup']],
				];
			} else {
				$items = [
	                    ['label' => '首页', 'url' => ['topic/index']],
						['label' => Html::encode($me->username), 'url' => ['user/setting']],
                        ['label' => '退出', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
				];
				if ($me->isInactive()) {
					$items[1]['options'] = ['class'=>'red'];
				}
			}

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $items,
            ]);
            NavBar::end();
        ?>
        <div class="container">
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
<p class="footer-links">
<?php
	if (!empty($settings['footer_links'])) {
		foreach($settings['footer_links'] as $link) {
			if ( strpos($link[1], 'http://') !== 0 && strpos($link[1], 'https://') !== 0 ) {
				$link[1] = 'http://'.$link[1];
			}
			echo Html::a($link[0], $link[1], ['rel' => 'external', 'target'=>'_blank']);
		}
	}
	if (!empty($settings['icp'])) {
		echo Html::a($settings['icp'], 'http://www.miibeian.gov.cn/', ['rel' => 'external', 'target'=>'_blank']);
	}
	if ( !$isGuest && $me->isAdmin() ) {
		echo Html::a('管理后台', ['admin/setting/all']);
	}
?>
</p>
<div>
<span>Powered by <a href="http://simpleforum.org/" rel="external" target="_blank">极简论坛 <?= SIMPLE_FORUM_VERSION ?></a></span>
<span><?= number_format( (microtime(true) - YII_BEGIN_TIME), 3) . 's' ?></span>
<?= !empty($settings['analytics_code'])?'<span>'.$settings['analytics_code'].'</span>':'' ?>
</div>
        </div>
    </footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
