<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\Url;

$settings = Yii::$app->params['settings'];
$url = Url::to(['site/activate', 'token'=>$token->token], true);

?>
<?=$username ?>, 您好<br />
<br />
您已经成功注册为<?=$settings['site_name'] ?>会员<br />
<br />
请点击以下链接激活此帐号：<br />
<?= Html::a($url, $url) ?><br />
<br />
感谢您的访问，祝您使用愉快！<br />
<br />
此致<br />
<?=$settings['site_name'] ?> 管理团队<br />
<?=Yii::$app->getRequest()->getHostInfo() ?><br />
