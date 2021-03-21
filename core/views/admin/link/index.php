<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = Yii::t('app/admin', 'Links');
?>

<div class="row">
<!-- sf-left start -->
<div class="col-lg-8 sf-left">

<ul class="list-group sf-box">
    <li class="list-group-item sf-box-header sf-navi">
        <p class='fr'><?php echo Html::a(Yii::t('app/admin', 'Add a link'), ['add']); ?></p>
        <?php
            echo Html::a(Yii::t('app/admin', 'Forum Manager'), ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title;
        ?>
    </li>
    <li class="list-group-item">
        <ul>
        <?php
            foreach($links as $link) {
                echo '<li>', $link['name'], '&nbsp;(', Html::a($link['url'], $link['url']), ')&nbsp;|&nbsp;', 
                    Html::a(Yii::t('app', 'Edit'), ['edit', 'id'=>$link['id']]), '&nbsp;|&nbsp;', 
                    Html::a(Yii::t('app', 'Delete'), ['delete', 'id'=>$link['id']], [
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete it? This operation cannot be undone.'),
                            'method' => 'post',
                    ]]), '</li>';
            }
        ?>
        </ul>
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
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->
</div>
