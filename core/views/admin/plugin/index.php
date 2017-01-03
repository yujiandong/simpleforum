<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '插件管理';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item">
        <p class='fr'><?php echo Html::a('可安装插件', ['installable']); ?></p>
        <?php
            echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
        ?>
    </li>
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
    foreach($plugins as $plugin) {
        echo '<tr><td>', $plugin['pid'], '</td>
			<td>', Html::a(Html::encode($plugin['name']), ['view', 'pid'=>$plugin['pid']]), '</td>
			<td>', Html::encode($plugin['version']) ,'</td><td>', 
            empty($plugin['settings'])?'':Html::a('配置', ['settings', 'pid'=>$plugin['pid']]).'&nbsp;|&nbsp;', 
            Html::a('卸载', ['uninstall', 'pid'=>$plugin['pid']], [
                'data' => [
                    'confirm' => '注意：请确认已经修改与此插件相关联的论坛配置！'."\n".'否则有可能会造成论坛出错。',
                    'method' => 'post',
            ]]), '</td>
		</tr>';
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
