<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap\Alert;

$session = Yii::$app->getSession();
$this->title = Yii::t('app', 'Add Topic');

?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
    <div class="panel-heading">
        <?php echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', Html::a(Html::encode($node['name']), ['topic/node', 'name'=>$node['ename']]), 
            '&nbsp;/&nbsp;', $this->title; ?>
    </div>
    <div class="panel-body">
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

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>

</div>
