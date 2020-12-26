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
<?php echo $settings['site_name']; ?> member<br />
<br />
There's one quick step you need to complete in order to confirm your new email address.<br />
<br />
Please click the link.<br />
<?php echo Html::a($url, $url); ?><br />
<br />
Thanks,<br />
<br />
<?php echo $settings['site_name']; ?> Team<br />
<?php echo Yii::$app->getRequest()->getHostInfo(); ?><br />
