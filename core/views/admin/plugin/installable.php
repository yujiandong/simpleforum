<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '可安装插件';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item">
        <?php
            echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', Html::a('插件管理', ['admin/plugin']), '&nbsp;/&nbsp;', $this->title;
        ?>
    </li>
    <li class="list-group-item list-group-item-info"><strong>插件</strong></li>
    <li class="list-group-item">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>插件ID</th>
          <th>插件名</th>
          <th>版本</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
<?php
    if( !empty($plugins) ) {
        foreach($plugins as $plugin) {
            echo '<tr><td>', $plugin['pid'], '</td>
				<td>', Html::a(Html::encode($plugin['name']), ['view', 'pid'=>$plugin['pid']]), '</td>
				<td>', Html::encode($plugin['version']) ,'</td>
				<td>', Html::a('安装', ['install', 'pid'=>$plugin['pid']]), '</td>
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
