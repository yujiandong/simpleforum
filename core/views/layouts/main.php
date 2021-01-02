<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

\app\assets\AppAsset::register($this);
$baseUrl = \Yii::$app->getRequest()->getBaseUrl();
$this->registerJs('var baseUrl = \''.$baseUrl.'\';', \yii\web\View::POS_HEAD);

$settings = Yii::$app->params['settings'];
$isGuest = Yii::$app->getUser()->getIsGuest();
if( !$isGuest ) {
    $me = Yii::$app->getUser()->getIdentity();
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>">
<head>
    <meta charset="<?php echo Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <?php echo Html::csrfMetaTags(); ?>
    <?php echo $settings['head_meta']; ?>
    <title><?php echo Html::encode($this->title), $this->title==$settings['site_name']?'':' - '.Html::encode($settings['site_name']); ?></title>
    <?php $this->head(); ?>
    <!--[if lt IE 9]>
      <script src="<?php echo $baseUrl; ?>/static/js/html5shiv.min.js"></script>
      <script src="<?php echo $baseUrl; ?>/static/js/respond.min.js"></script>
    <![endif]-->
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
                        ['label' => '<i class="fa fa-home"></i>'.Yii::t('app', 'Home'), 'url' => ['topic/index']],
                        ['label' => '<i class="fa fa-sign-in"></i>'.Yii::t('app', 'Sign in'), 'url' => ['/site/login']],
                        ['label' => '<i class="fa fa-user-plus"></i>'.Yii::t('app', 'Sign up'), 'url' => ['/site/signup']],
                ];
            } else {
                $items = [
                        ['label' => '<i class="fa fa-home"></i>'.Yii::t('app', 'Home'), 'url' => ['topic/index']],
                        ['label' => '<i class="fa fa-user"></i>'.Html::encode($me->username), 'url' => ['user/view', 'username'=>$me->username]],
                        ['label' => '<i class="fa fa-cog"></i>'.Yii::t('app', 'Settings'), 'url' => ['my/settings']],
                        ['label' => '<i class="fa fa-sign-out"></i>'.Yii::t('app', 'Sign out'), 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
                ];
                if ($me->isInactive()) {
                    $items[1]['options'] = ['class'=>'red'];
                }
            }
echo '<form class="navbar-form navbar-left" action="'.Url::to(['topic/search']).'" role="search">
       <div class="form-group input-group">
            <span class="input-group-addon"><i class="fa fa-search fa-no-mr"></i></span>
            <input id="q" name="q" type="search" placeholder="'.Yii::t('app', 'Search').'" maxlength="40" class="form-control">
        </div>
  </form>';
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $items,
                'encodeLabels'=>false,
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
<?php
    if (!empty($settings['footer_links'])) {
        foreach($settings['footer_links'] as $link) {
            if ( strpos($link[1], 'http://') !== 0 && strpos($link[1], 'https://') !== 0 ) {
                $link[1] = 'http://'.$link[1];
            }
            echo '<li>', Html::a($link[0], $link[1], ['rel' => 'external', 'target'=>'_blank']), '</li>';
        }
    }
    if ( !$isGuest && $me->isAdmin() ) {
        echo '<li>', Html::a(Yii::t('app', 'Admin Panel'), ['admin/setting/all']), '</li>';
    }
?>
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
<span><?php echo number_format( (microtime(true) - YII_BEGIN_TIME), 3) . 's'; ?></span>
<?php echo !empty($settings['analytics_code'])?'<span>'.$settings['analytics_code'].'</span>':''; ?>
</div>
        </div>
    </footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
