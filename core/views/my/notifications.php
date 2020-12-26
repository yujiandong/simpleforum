<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Notice;
use app\models\Topic;
use app\components\SfHtml;

$this->title = Yii::t('app', 'Notifications');
$formatter = Yii::$app->getFormatter();

$classSys = [];
$classSms = [];
if( Yii::$app->getRequest()->get('type') == 'sms' ) {
    $classSms = ['class'=>'btn btn-sm btn-primary current'];
} else {
    $classSys = ['class'=>'btn btn-sm btn-primary current'];
}

?>

<div class="row">
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item navi-top-list">
    <?php echo Html::a(Yii::t('app', 'Notifications'). ($sysCount>0?'('.$sysCount.')':''), ['my/notifications'], $classSys), Html::a(Yii::t('app', 'Messages'). ($smsCount>0?'('.$smsCount.')':''), ['my/notifications', 'type'=>'sms'], $classSms); ?>
    </li>
<?php
foreach($notices as $notice) {
    if ( $notice['topic_id'] > 0 && empty(ArrayHelper::getValue($notice, 'topic', []))) {
        continue;
    }
    echo '<li class="list-group-item media">',
        SfHtml::uImgLink($notice['source'], 'small', ['class'=>'media-left']),
        '<div class="media-body">
            <span class="fr gray small">', $formatter->asRelativeTime($notice['created_at']), '</span>',
            SfHtml::uLink($notice['source']['username']), ' ';
            if($notice['type'] == Notice::TYPE_COMMENT) {
                echo Yii::t('app', 'commented on your topic [{title}].', ['title'=>Html::a(Html::encode($notice['topic']['title']), Topic::getRedirectUrl($notice['topic_id'], $notice['position']))]),
                    $notice['notice_count']>0?'<span class="small gray">('. Yii::t('app', '+{n, plural, =1{# time} other{# times}}', ['n'=>intval($notice['notice_count'])]) . ')</span>':'';
            } else if($notice['type'] == Notice::TYPE_MENTION) {
                if ($notice['position'] > 0) {
                    echo Yii::t('app', 'mentioned you in a comment on the topic [{title}].', ['title'=>Html::a(Html::encode($notice['topic']['title']), Topic::getRedirectUrl($notice['topic_id'], $notice['position']))]);
                } else {
                    echo Yii::t('app', 'mentioned you in the topic [{title}].', ['title'=>Html::a(Html::encode($notice['topic']['title']), ['topic/view', 'id'=>$notice['topic_id']])]);
                }
            } else if($notice['type'] == Notice::TYPE_FOLLOW_TOPIC) {
                echo Yii::t('app', 'favorited your topic [{title}].', ['title'=>Html::a(Html::encode($notice['topic']['title']), ['topic/view', 'id'=>$notice['topic_id']])]);
            } else if($notice['type'] == Notice::TYPE_FOLLOW_USER) {
                    echo Yii::t('app', 'followed you.');
            } else if($notice['type'] == Notice::TYPE_MSG) {
                    echo Yii::t('app', 'sent you a message.'), ' ', Html::a('<i class="fa fa-reply" aria-hidden="true"></i>'. Yii::t('app', 'Reply'), ['service/sms', 'id'=>$notice['id']]);
                    echo '<br />', Html::encode($notice['msg']);
            } else if($notice['type'] == Notice::TYPE_GOOD_TOPIC) {
                echo Yii::t('app', '{username} thanked you for your topic > {url}', ['username'=>'', 'url'=>Html::a(Html::encode($notice['topic']['title']), ['topic/view', 'id'=>$notice['topic_id']])]);
            } else if($notice['type'] == Notice::TYPE_GOOD_COMMENT) {
                echo Yii::t('app', '{username} thanked you for your comment on > {url}', ['username'=>'', 'url'=>Html::a(Html::encode($notice['topic']['title']), Topic::getRedirectUrl($notice['topic_id'], $notice['position']))]);
            } else if($notice['type'] == Notice::TYPE_CHARGE_POINT) {
                echo Yii::t('app', 'charged points to you with a message: {msg}', ['msg' => Html::encode($notice['msg'])]);
            }
        echo '</div>';
    echo '</li>';
}
?>
    <li class="list-group-item item-pagination">
    <?php
    echo LinkPager::widget([
        'pagination' => $pages,
        'maxButtonCount'=>5,
    ]);
    ?>
    </li>

</ul>
</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
