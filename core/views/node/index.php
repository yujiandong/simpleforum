<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use app\models\Navi;
use app\models\Node;

$settings = Yii::$app->params['settings'];
$this->title = Yii::t('app', 'All Nodes');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 col-sm-12 sf-left">

<?php
if ( intval($settings['cache_enabled']) ===0 || $this->beginCache('f-all-nodes', ['duration' => intval($settings['cache_time'])*60])) :
?>
<div class="panel panel-default sf-box">
    <div class="panel-heading">
    <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title; ?>
    </div>
</div>
<?php
    $navis = Navi::getAllNaviNodes();
    foreach($navis as $cNavi) :
?>
<div class="panel panel-default sf-box">
    <div class="panel-heading">
    <span class="fr gray"><?php echo Yii::t('app', '{n, plural, =0{no nodes} =1{# node} other{# nodes}}', ['n'=>count($cNavi['naviNodes'])]); ?></span>
    <?php echo Html::encode($cNavi['name']); ?>
    </div>
    <div class="panel-body hot-nodes sf-btn">
<?php
    foreach($cNavi['naviNodes'] as $cNode) {
        $cNode = $cNode['node'];
        echo Html::a(Html::encode($cNode['name']), ['topic/node', 'name'=>$cNode['ename']], ['class'=>'btn btn-default']);
    }
?>
    </div>
</div>
<?php
    endforeach;
    $nodes = Node::getNodesWithoutNavi();
?>
<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <span class="fr gray"><?php echo Yii::t('app', '{n, plural, =0{no nodes} =1{# node} other{# nodes}}', ['n' => count($nodes)]); ?></span><?php echo Yii::t('app', 'Default navi'); ?>
    </div>
    <div class="panel-body hot-nodes sf-btn">
<?php
    foreach($nodes as $cNode) {
        echo Html::a(Html::encode($cNode['name']), ['topic/node', 'name'=>$cNode['ename']], ['class'=>'btn btn-default']);
    }
?>
    </div>
</div>

<?php
if ( intval($settings['cache_enabled']) !== 0 ) {
    $this->endCache();
}
endif;
?>

</div>
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
<!-- sf-right end -->

</div>
