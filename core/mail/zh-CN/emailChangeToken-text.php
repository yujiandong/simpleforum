<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\Url;

$settings = Yii::$app->params['settings'];
$url = Url::to(['site/verify-email', 'token'=>$token->token], true);

?>
<?php echo $settings['site_name']; ?> 会员，您好<br />
<br />
您申请了绑定新邮箱，请点击以下链接确认：<br />
<?php echo Html::a($url, $url); ?><br />
<br />
感谢您的访问，祝您使用愉快！<br />
<br />
此致<br />
<?php echo $settings['site_name']; ?> 管理团队<br />
<?php echo Yii::$app->getRequest()->getHostInfo(); ?><br />
