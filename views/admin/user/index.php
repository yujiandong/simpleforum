<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\User;

$this->title = '用户管理';
?>

<div class="row">
<!-- sf-left start -->
<div class="col-md-8 sf-left">

<ul class="list-group sf-box">
	<li class="list-group-item">
		<?php echo Html::a('论坛管理', ['admin/setting/all']), '&nbsp;/&nbsp;', $this->title; ?>
	</li>
	<li class="list-group-item list-group-item-info"><strong>
		<?php echo Html::a('待激活用户', ['index', 'status'=>User::STATUS_INACTIVE]),
				'&nbsp;|&nbsp;', Html::a('待验证用户', ['index', 'status'=>User::STATUS_ADMIN_VERIFY]),
				'&nbsp;|&nbsp;', Html::a('屏蔽用户', ['index', 'status'=>User::STATUS_BANNED]); ?></strong>
	</li>
	<li class="list-group-item">
		<ul>
		<?php
			foreach($users as $user) {
				echo '<li>', Html::a(Html::encode($user['username']), ['info', 'id'=>$user['id']]), '&nbsp;|&nbsp;', Html::a('激活', ['activate', 'id'=>$user['id']]), '</li>';
			}
		?>
		</ul>
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
<!-- sf-left end -->

<!-- sf-right start -->
<div class="col-md-4 sf-right">
<?php echo $this->render('@app/views/common/_admin-right'); ?>
</div>
<!-- sf-right end -->

</div>
