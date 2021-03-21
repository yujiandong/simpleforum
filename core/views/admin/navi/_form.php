<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\models\Navi;

?>

<?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'id' => 'form-navi',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-form-label col-sm-3 text-sm-right',
                    'wrapper' => 'col-sm-9',
                ],
            ],
       ]);

$naviTypes = Navi::getTypes();

if($type === 'edit') {
?>
        <div class="form-group row">
            <label class="col-form-label col-sm-3 text-sm-right"><?php echo Yii::t('app', 'Type'); ?></label>
            <div class="col-sm-9">
                <strong class="form-control bg-light"><?php echo $naviTypes[$model->type]; ?></strong>
            </div>
        </div>

<?php
} else {
    echo $form->field($model, 'type')->dropDownList($naviTypes);
}
    echo $form->field($model, 'name')->textInput(['maxlength' => 20]);
    echo $form->field($model, 'ename')->textInput(['maxlength' => 20]);
    echo $form->field($model, 'sortid')->textInput(['maxlength' => 2])->hint(Yii::t('app/admin', 'The results will be sorted from smallest to largest. Default value is {n}.', ['n'=>50]));
?>
    <div class="form-group">
        <div class="offset-sm-3 col-sm-9">
            <?php echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Edit'), ['class' => 'btn sf-btn']); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
