<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = Yii::t('app', 'My favorite nodes');
?>

<div class="row">
<div class="col-lg-8 sf-left">

<div class="card sf-box">
    <div class="card-header sf-box-header sf-navi">
        <?php
            echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title;
        ?>
    </div>
    <div class="card-body">
        <ul>
        <?php
            foreach($nodes as $node) {
                echo '<li>', Html::a(Html::encode($node['node']['name']), ['topic/node', 'name'=>$node['node']['ename']]) , '</li>';
            }
        ?>
        </ul>
    </div>
    <div class="card-footer bg-transparent sf-pagination">
    <?php
    echo LinkPager::widget([
        'pagination' => $pages,
        'maxButtonCount'=>5,
        'listOptions' => ['class'=>'pagination justify-content-center my-2'],
        'activeLinkCssClass' => ['sf-btn'],
    ]);
    ?>
    </div>
</div>


</div>

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
