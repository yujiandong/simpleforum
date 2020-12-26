<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\helpers\Url;

$settings = Yii::$app->params['settings'];
$url = Url::to(['site/reset-password', 'token'=>$token->token], true);

?>
<?php echo $settings['site_name']; ?>会員様<br />
<br />
下記URLにアクセスしてパスワードリセットを完了させてください。<br />

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