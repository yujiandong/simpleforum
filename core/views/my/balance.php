<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\History;
use app\lib\Util;

$this->title = '账户余额';
$formatter = Yii::$app->getFormatter();
$me = Yii::$app->getUser()->getIdentity();

$types = [
    History::ACTION_REG => '注册帐号',
    History::ACTION_ADD_TOPIC => '发表主题',
    History::ACTION_ADD_COMMENT => '回复主题',
    History::ACTION_COMMENTED => '主题回复收益',
    History::ACTION_ORIGINAL_SCORE => '初始积分',
    History::ACTION_SIGNIN => '每日登录奖励',
    History::ACTION_SIGNIN_10DAYS => '连续登录奖励',
    History::ACTION_INVITE_CODE => '购买邀请码',
    History::ACTION_MSG => '发送私信',
];

function getComment($action, $ext) {
    $str = '';
    if( $action == History::ACTION_ADD_TOPIC ) {
        $str = '发表了主题 › ' . Html::a(Html::encode($ext['title']), ['topic/view', 'id'=>$ext['topic_id']]);
    } else if( $action == History::ACTION_ADD_COMMENT ) {
        $str = '回复了主题 › ' . Html::a(Html::encode($ext['title']), ['topic/view', 'id'=>$ext['topic_id']]);
    } else if( $action == History::ACTION_ORIGINAL_SCORE ) {
        $str = '获得初始积分 ' . $ext['cost'];
    } else if( $action == History::ACTION_COMMENTED ) {
        $str = '收到 ' . Html::a(Html::encode($ext['commented_by']), ['user/view', 'username'=>$ext['commented_by']]) . ' 的回复 › '. Html::a(Html::encode($ext['title']), ['topic/view', 'id'=>$ext['topic_id']]);
    } else if( $action == History::ACTION_SIGNIN ) {
        $str = '每日登录奖励 ' . $ext['cost'] . ' 积分';
    } else if( $action == History::ACTION_SIGNIN_10DAYS ) {
        $str = '连续登录每 10 天奖励 ' . $ext['cost'] . ' 积分';
    } else if( $action == History::ACTION_INVITE_CODE ) {
        $str = '购买了 ' . $ext['amount'] . ' 枚邀请码';
    } else if( $action == History::ACTION_REG ) {
        $str = '注册帐号奖励 ' . $ext['cost'] . ' 积分';
    } else if( $action == History::ACTION_MSG ) {
        $str = '给 ' . Html::a(Html::encode($ext['target']), ['user/view', 'username'=>$ext['target']]) . ' 发送私信';
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
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item">
    <?php echo Html::a('首页', ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </li>
    <li class="list-group-item">
        <h4>当前账户余额： <?php echo Util::getScore($me->score); ?></h4>
    </li>
    <li class="list-group-item">
    <table class="table table-condensed table-bordered small">
      <thead>
        <tr>
          <th>类型/时间</th>
          <th>数额</th>
          <th>余额</th>
          <th>描述</th>
        </tr>
      </thead>
      <tbody>
<?php
foreach($records as $record) {
    $ext = json_decode($record['ext'], true);
    echo '<tr>',
            '<td width="120">', $types[$record['action']], '<br />', $formatter->asDateTime($record['action_time'], 'y-MM-dd HH:mm'), '</td>',
            '<td width="50" class="text-right"><strong>', getCostName($ext['cost']), '</strong></td>',
            '<td width="60" class="text-right">', $ext['score'], '</td>',
            '<td width="auto">', getComment($record['action'], $ext), '</td>',
         '</tr>';
}
?>
      </tbody>
    </table>
    </li>
    <li class="list-group-item item-pagination">
    <?php
    echo LinkPager::widget([
        'pagination' => $pages,
    ]);
    ?>
    </li>

</ul>
</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
