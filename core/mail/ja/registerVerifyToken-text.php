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
<?php echo $username; ?>様<br />
<br />
<?php echo $settings['site_name']; ?>会員にご登録いただき、誠にありがとうございます。<br />
<br />
下記URLにアクセスして会員登録を完了させてください。<br />
この手続きが完了するまでは会員状態が有効になりませんのでご注意ください。<br />

<?php echo Html::a($url, $url); ?><br />
※クリックしてもアクセスできない場合は、上記URLをコピーして、ブラウザのアドレスバーにペーストしてください。<br />
<br />
------------------------------------------------------------<br />
★ご注意：<br />
このメールは配信専用となっております。<br />
このメールアドレスにご返信いただいてもご返事ができません。<br />
------------------------------------------------------------<br />
<?php echo $settings['site_name']; ?><br />
<?php echo Yii::$app->getRequest()->getHostInfo(); ?><br />
------------------------------------------------------------