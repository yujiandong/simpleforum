<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
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
      <script src="<?php echo $baseUrl; ?>/static/assets/bootstrap/html5shiv.min.js"></script>
      <script src="<?php echo $baseUrl; ?>/static/assets/bootstrap/respond.min.js"></script>
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
                        ['label' => '<i class="fa fa-home"></i>首页', 'url' => ['topic/index']],
                        ['label' => '<i class="fa fa-sign-in"></i>登录', 'url' => ['/site/login']],
                        ['label' => '<i class="fa fa-user-plus"></i>注册', 'url' => ['/site/signup']],
                ];
            } else {
                $items = [
                        ['label' => '<i class="fa fa-home"></i>首页', 'url' => ['topic/index']],
                        ['label' => '<i class="fa fa-user"></i>'.Html::encode($me->username), 'url' => ['user/view', 'username'=>$me->username]],
                        ['label' => '<i class="fa fa-cog"></i>设置', 'url' => ['my/settings']],
                        ['label' => '<i class="fa fa-sign-out"></i>退出', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
                ];
                if ($me->isInactive()) {
                    $items[1]['options'] = ['class'=>'red'];
                }
            }
echo '<form class="navbar-form navbar-left" action="'.Url::to(['topic/search']).'" role="search">
       <div class="form-group input-group">
            <span class="input-group-addon"><i class="fa fa-search fa-no-mr"></i></span>
            <input id="q" name="q" type="search" placeholder="搜索" maxlength="40" class="form-control">
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
