<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = Yii::t('app/admin', 'Installable plugins');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item">
        <?php
			echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', Html::a(Yii::t('app/admin', 'Plugins'), ['index']), '&nbsp;/&nbsp;', $this->title;
        ?>
    </li>
    <li class="list-group-item list-group-item-info"><strong>插件</strong></li>
    <li class="list-group-item">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th><?php echo Yii::t('app/admin', 'Plugin id'); ?></th>
          <th><?php echo Yii::t('app/admin', 'Plugin name'); ?></th>
          <th><?php echo Yii::t('app', 'Version'); ?></th>
          <th><?php echo Yii::t('app/admin', 'Operation'); ?></th>
        </tr>
      </thead>
      <tbody>
<?php
    if( !empty($plugins) ) {
        foreach($plugins as $plugin) {
            echo '<tr><td>', $plugin['pid'], '</td>
				<td>', Html::a(Html::encode($plugin['name']), ['view', 'pid'=>$plugin['pid']]), '</td>
				<td>', Html::encode($plugin['version']) ,'</td>
				<td>', Html::a(Yii::t('app/admin', 'Install'), ['install', 'pid'=>$plugin['pid']]), '</td>
			</tr>';
        }
    }
?>
      </tbody>
    </table>
    </li>
</ul>


</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->
</div>
