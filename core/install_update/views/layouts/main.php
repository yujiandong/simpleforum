<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;

\app\assets\AppAsset::register($this);

$settings = Yii::$app->params['settings'];
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
    <title><?php echo Html::encode($this->title); ?></title>
    <?php $this->head() ?>
    <style>
        table {font-size:14px;}
    </style>
</head>
<body>

<?php $this->beginBody() ?>
    <header class="sticky-top">
      <nav class="navbar navbar-expand-md navbar-dark container-md">
        <a class="navbar-brand" href="<?php echo Yii::$app->homeUrl; ?>"><?php echo $settings['site_name']; ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      </nav>
    </header>
    <div class="wrap">
        <div class="container-md">
            <?php echo $content; ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container-md">
<ul class="footer-links list-inline">
    <!-- language selector start -->
    <li class="dropup list-inline-item">
            <a id="drop4" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              Language<span class="caret"></span>
            </a>
            <ul id="menu1" class="dropdown-menu" aria-labelledby="drop4">
              <li class="dropdown-item"><?php echo Html::a('English', ['/site/language', 'language'=>'en-US']); ?></li>
              <li class="dropdown-item"><?php echo Html::a('简体中文', ['/site/language', 'language'=>'zh-CN']); ?></li>
              <li class="dropdown-item"><?php echo Html::a('日本語', ['/site/language', 'language'=>'ja']); ?></li>
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
