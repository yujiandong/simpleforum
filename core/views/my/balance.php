<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\History;
use app\components\SfHtml;

$this->title = Yii::t('app', 'My Points');
$formatter = Yii::$app->getFormatter();
$me = Yii::$app->getUser()->getIdentity();

$types = [
    History::ACTION_REG => Yii::t('app', 'Sign up'),
    History::ACTION_ADD_TOPIC => Yii::t('app', 'Add Topic'),
    History::ACTION_ADD_COMMENT => Yii::t('app', 'Add Comment'),
    History::ACTION_COMMENTED => Yii::t('app', 'Topic Commented'),
    History::ACTION_ORIGINAL_SCORE => Yii::t('app', 'Original Points'),
    History::ACTION_SIGNIN => Yii::t('app', 'Daily Bonus'),
    History::ACTION_SIGNIN_10DAYS => Yii::t('app', '10 Days Bonus'),
    History::ACTION_INVITE_CODE => Yii::t('app', 'Buy Invite Codes'),
    History::ACTION_MSG => Yii::t('app', 'Send Message'),
    History::ACTION_GOOD_TOPIC => Yii::t('app', 'Thank Topic'),
    History::ACTION_GOOD_COMMENT => Yii::t('app', 'Thank Comment'),
    History::ACTION_TOPIC_THANKED => Yii::t('app', 'Topic Thanked'),
    History::ACTION_COMMENT_THANKED => Yii::t('app', 'Comment Thanked'),
    History::ACTION_CHARGE_POINT => Yii::t('app', 'Charge Points'),
];

function getComment($action, $ext) {
    $str = '';
    if( $action == History::ACTION_ADD_TOPIC ) {
        $str = Yii::t('app', 'Added a topic › {url}', ['url' => Html::a(Html::encode($ext['title']), ['topic/view', 'id'=>$ext['topic_id']])]);
    } else if( $action == History::ACTION_ADD_COMMENT ) {
        $str = Yii::t('app', 'Added a comment on › {url}', ['url' => Html::a(Html::encode($ext['title']), ['topic/view', 'id'=>$ext['topic_id']])]);
    } else if( $action == History::ACTION_ORIGINAL_SCORE ) {
        $str = Yii::t('app', 'Got {n} original points', ['n' => $ext['cost']]);
    } else if( $action == History::ACTION_COMMENTED ) {
        $str = Yii::t('app', 'Received a comment from {username} on > {url}', ['username' => Html::a(Html::encode($ext['commented_by']), ['user/view', 'username'=>$ext['commented_by']]), 'url' => Html::a(Html::encode($ext['title']), ['topic/view', 'id'=>$ext['topic_id']])]);
    } else if( $action == History::ACTION_SIGNIN ) {
        $str = Yii::t('app', 'Got {n} bonus points', ['n' => $ext['cost']]);
    } else if( $action == History::ACTION_SIGNIN_10DAYS ) {
        $str = Yii::t('app', 'Got {n} bonus points for consecutive 10 days', ['n' => $ext['cost']]);
    } else if( $action == History::ACTION_INVITE_CODE ) {
        $str = Yii::t('app', 'Bought {n, plural, =1{# invite code} other{# invite codes}}', ['n' => (int)$ext['amount']]);
    } else if( $action == History::ACTION_REG ) {
        $str = Yii::t('app', 'Got {n} points for registration', ['n' => $ext['cost']]);
    } else if( $action == History::ACTION_MSG ) {
        $str = Yii::t('app', 'Sent a message to {username}', ['username' => Html::a(Html::encode($ext['target']), ['user/view', 'username'=>$ext['target']])]);
    } else if( $action == History::ACTION_GOOD_TOPIC ) {
        $str = Yii::t('app', 'Thanked for {username}\'s topic > {url}', ['username' => SfHtml::uLink($ext['thank_to']), 'url' => Html::a(Html::encode($ext['title']), ['topic/view', 'id'=>$ext['topic_id']])]);
    } else if( $action == History::ACTION_GOOD_COMMENT ) {
        $str = Yii::t('app', 'Thanked for {username}\'s comment on > {url}', ['username' => SfHtml::uLink($ext['thank_to']), 'url'=>Html::a(Html::encode($ext['title']), ['topic/view', 'id'=>$ext['topic_id']])]);
    } else if( $action == History::ACTION_TOPIC_THANKED ) {
        $str = Yii::t('app', '{username} thanked you for your topic > {url}', ['username' => SfHtml::uLink($ext['thank_by']), 'url'=>Html::a(Html::encode($ext['title']), ['topic/view', 'id'=>$ext['topic_id']])]);
    } else if( $action == History::ACTION_COMMENT_THANKED ) {
        $str = Yii::t('app', '{username} thanked you for your comment on > {url}', ['username' => SfHtml::uLink($ext['thank_by']), 'url'=>Html::a(Html::encode($ext['title']), ['topic/view', 'id'=>$ext['topic_id']])]);
    } else if( $action == History::ACTION_CHARGE_POINT ) {
        $str = Yii::t('app', 'Charged {n} points. Message: {msg}', ['n' => $ext['cost'], 'msg' => Html::encode($ext['msg'])]);
    }
    return $str;
}
function getCostName($cost) {
    $cost = intval($cost);
    $color = $cost>0?'blue':'red';
    return '<span class="' . $color . '">' . $cost . '</span>';
}

?>

<div class="row">
<div class="col-lg-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item sf-box-header sf-navi">
    <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </li>
    <li class="list-group-item">
        <h4><?php echo Yii::t('app', 'My Points'); ?>： <?php echo SfHtml::uScore($me->score); ?></h4>
    </li>
    <li class="list-group-item">
    <table class="table table-condensed table-bordered small">
      <thead>
        <tr>
          <th style="min-width:90px;"><?php echo Yii::t('app', 'Type/Time'); ?></th>
          <th style="min-width:50px;"><?php echo Yii::t('app', 'Points'); ?></th>
          <th style="min-width:50px;"><?php echo Yii::t('app', 'Total'); ?></th>
          <th><?php echo Yii::t('app', 'Description'); ?></th>
        </tr>
      </thead>
      <tbody>
<?php
foreach($records as $record) {
    $ext = json_decode($record['ext'], true);
    echo '<tr>',
            '<td>', $types[$record['action']], '<br />', $formatter->asDateTime($record['action_time'], 'y-MM-dd HH:mmZ'), '</td>',
            '<td class="text-right"><strong>', getCostName($ext['cost']), '</strong></td>',
            '<td class="text-right">', $ext['score'], '</td>',
            '<td>', getComment($record['action'], $ext), '</td>',
         '</tr>';
}
?>
      </tbody>
    </table>
    </li>
    <li class="list-group-item sf-pagination">
    <?php
    echo LinkPager::widget([
        'pagination' => $pages,
        'maxButtonCount'=>5,
        'listOptions' => ['class'=>'pagination justify-content-center my-2'],
        'activeLinkCssClass' => ['sf-btn'],
    ]);
    ?>
    </li>

</ul>
</div>

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
