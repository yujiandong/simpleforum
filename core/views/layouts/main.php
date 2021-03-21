<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\Nav;
//use yii\bootstrap4\NavBar;

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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
    <header class="sticky-top">
      <nav class="navbar navbar-expand-md navbar-dark container-md">
        <a class="navbar-brand" href="<?php echo Yii::$app->homeUrl; ?>"><?php echo $settings['site_name']; ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <?php
/*            NavBar::begin([
                'brandLabel' => $settings['site_name'],
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar navbar-expand-lg navbar-dark',
                ],
            ]);
*/
            if ($isGuest) {
                $items = [
                        ['label' => '<i class="fa fa-home"></i>'.Yii::t('app', 'Home'), 'url' => ['topic/index']],
                        ['label' => '<i class="fas fa-sign-in-alt"></i>'.Yii::t('app', 'Sign in'), 'url' => ['/site/login']],
                        ['label' => '<i class="fa fa-user-plus"></i>'.Yii::t('app', 'Sign up'), 'url' => ['/site/signup']],
                ];
            } else {
                $items = [
                        ['label' => '<i class="fa fa-home"></i>'.Yii::t('app', 'Home'), 'url' => ['topic/index']],
                        ['label' => '<i class="fa fa-user"></i>'.Html::encode($me->username), 'url' => ['user/view', 'username'=>$me->username]],
                        ['label' => '<i class="fa fa-cog"></i>'.Yii::t('app', 'Settings'), 'url' => ['my/settings']],
                        ['label' => '<i class="fas fa-sign-out-alt"></i>'.Yii::t('app', 'Sign out'), 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
                ];
                if ($me->isInactive()) {
                    $items[1]['options'] = ['class'=>'red'];
                }
            }
  echo '<div id="navbarNavDropdown" class="collapse navbar-collapse">';
  echo   '<form class="form-inline mr-auto" action="'.Url::to(['topic/search']).'" role="search">
<div class="input-group">
  <div class="input-group-prepend">
    <i class="input-group-text fas fa-lg fa-search mr-0 align-middle"></i>
  </div>
  <input type="text" id="q" name="q" class="form-control" placeholder="'.Yii::t('app', 'Search').'" maxlength="40" aria-label="Search">
</div>
          </form>';
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => $items,
                'encodeLabels'=>false,
            ]);
  echo '</div>';
//            NavBar::end();
        ?>
      </nav>
    </header>
    <div class="wrap">
        <div class="container-md">
            <?php echo $content; ?>
        </div>
    </div>

    <footer>
        <div class="container-md">
<ul class="footer-links list-inline">
<?php
    if (!empty($settings['footer_links'])) {
        foreach($settings['footer_links'] as $link) {
            if ( strpos($link[1], 'http://') !== 0 && strpos($link[1], 'https://') !== 0 ) {
                $link[1] = 'http://'.$link[1];
            }
            echo '<li class="list-inline-item">', Html::a($link[0], $link[1], ['rel' => 'external', 'target'=>'_blank']), '</li>';
        }
    }
    if ( !$isGuest && $me->isAdmin() ) {
        echo '<li class="list-inline-item">', Html::a(Yii::t('app', 'Admin Panel'), ['admin/setting/all']), '</li>';
    }
?>
    <!-- language selector start -->
    <li class="dropup list-inline-item">
            <a id="drop4" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              Language<span class="caret"></span>
            </a>
            <ul id="menu1" class="dropdown-menu" aria-labelledby="drop4">
              <li class="dropdown-item"><?php echo Html::a('English', ['site/language', 'language'=>'en-US']); ?></li>
              <li class="dropdown-item"><?php echo Html::a('日本語', ['site/language', 'language'=>'ja']); ?></li>
              <li class="dropdown-item"><?php echo Html::a('简体中文', ['site/language', 'language'=>'zh-CN']); ?></li>
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
