<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\Url;

$settings = Yii::$app->params['settings'];
$url = Url::to(['site/activate', 'token'=>$token->token], true);

?>
<?php echo $username; ?><br />
<br />
Thank you for your registration!<br />
<br />
Please click the link to activate your account：<br />
<?php echo Html::a($url, $url); ?><br />
<br />
Thanks，<br />
<br />
<?php echo $settings['site_name']; ?> Team<br />
<?php echo Yii::$app->getRequest()->getHostInfo(); ?><br />
