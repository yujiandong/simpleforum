<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\Alert;

$session = Yii::$app->getSession();
$this->title = Yii::t('app', 'Add Topic');

?>

<div class="row">
<div class="col-lg-8 sf-left">

<div class="card sf-box">
    <div class="card-header sf-box-header sf-navi">
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', Html::a(Html::encode($node['name']), ['topic/node', 'name'=>$node['ename']]), 
            '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="card-body">
<?php
if ( $session->hasFlash('postNG') ) {
echo Alert::widget([
       'options' => ['class' => 'alert-warning'],
       'body' => Yii::t('app', $session->getFlash('postNG')),
    ]);
}
?>
    <?php echo $this->render('_form', [
        'model' => $model,
        'content' => $content,
        'action' => 'add',
    ]); ?>
    </div>
</div>

</div>

<div class="col-lg-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
