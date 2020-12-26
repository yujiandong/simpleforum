<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\i18n\Formatter;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'My favorite nodes');
?>

<div class="row">
<div class="col-md-8 sf-left">

<div class="panel panel-default sf-box">
	<div class="panel-heading">
		<?php
			echo Html::a(Yii::t('app', 'Home'), ['topic/index']), '&nbsp;/&nbsp;', $this->title;
		?>
	</div>
	<div class="panel-body">
		<ul>
		<?php
			foreach($nodes as $node) {
				echo '<li>', Html::a(Html::encode($node['node']['name']), ['topic/node', 'name'=>$node['node']['ename']]) , '</li>';
			}
		?>
		</ul>
	</div>
	<div class="panel-footer item-pagination">
	<?php
	echo LinkPager::widget([
	    'pagination' => $pages,
		'maxButtonCount'=>5,
	]);
	?>
	</div>
</div>


</div>

<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_right'); ?>
</div>
