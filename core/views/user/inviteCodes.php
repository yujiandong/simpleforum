<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\History;
use app\models\Token;
use app\lib\Util;

$this->title = '我的邀请码';
$formatter = Yii::$app->getFormatter();
$me = Yii::$app->getUser()->getIdentity();

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
    <table class="table table-condensed table-bordered">
      <thead>
        <tr>
          <th>邀请码</th>
          <th>有效期</th>
          <th>状态</th>
        </tr>
      </thead>
      <tbody>
<?php
foreach($records as $record) {
//    $ext = unserialize($record['ext']);
    echo '<tr', ($record['status']==0)?'':' class="active"', '>',
            '<td>', ($record['status']==0)?$record['token']:'<del>'.$record['token'].'</del>', '</td>',
            '<td>', ($record['expires'] == 0)?'永久有效':$formatter->asDateTime($record['expires'], 'y-MM-dd HH:mm:ss'), '</td>',
            '<td>', ($record['status']==0)?'未使用':'已使用', '</td>',
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
