<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\History;
use app\models\Token;
use app\components\SfHtml;

$this->title = Yii::t('app', 'My Invite Codes');
$formatter = Yii::$app->getFormatter();
$me = Yii::$app->getUser()->getIdentity();

?>

<div class="row">
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item">
    <span class="fr"><?php echo Html::a(Yii::t('app', 'Buy Invite Codes'), ['service/buy-invite-code']); ?></span>
    <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </li>
    <li class="list-group-item">
        <h4><?php echo Yii::t('app', 'My Points'); ?>： <?php echo SfHtml::uScore($me->score); ?></h4>
    </li>
    <li class="list-group-item">
    <table class="table table-condensed table-bordered">
      <thead>
        <tr>
          <th><?php echo Yii::t('app', 'Invite code'); ?></th>
          <th><?php echo Yii::t('app', 'Expiry date'); ?></th>
          <th><?php echo Yii::t('app', 'Status'); ?></th>
        </tr>
      </thead>
      <tbody>
<?php
foreach($records as $record) {
//    $ext = unserialize($record['ext']);
    echo '<tr', ($record['status']==0)?'':' class="active"', '>',
            '<td>', ($record['status']==0)?$record['token']:'<del>'.$record['token'].'</del>', '</td>',
            '<td>', ($record['expires'] == 0)?Yii::t('app', 'Indefinite period'):$formatter->asDateTime($record['expires'], 'y-MM-dd HH:mm:ssZ'), '</td>',
            '<td>', ($record['status']==0)?Yii::t('app', 'Unused'):Yii::t('app', 'Used'), '</td>',
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
