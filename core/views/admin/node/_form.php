<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

?>

<?php $form = ActiveForm::begin([
          'layout' => 'horizontal',
          'id' => 'form-node',
          'fieldConfig' => [
              'horizontalCssClasses' => [
                  'label' => 'col-form-label col-sm-3 text-sm-right',
                  'wrapper' => 'col-sm-9',
              ],
          ],
      ]); ?>

<?php
    echo $form->field($model, 'name')->textInput(['maxlength' => 20]);
    echo $form->field($model, 'ename')->textInput(['maxlength' => 20]);
    echo $form->field($model, 'access_auth', [
                       'horizontalCssClasses' => [
                           'offset' => 'offset-sm-3',
                       ]
                   ])->checkbox();
    echo $form->field($model, 'invisible', [
                       'horizontalCssClasses' => [
                           'offset' => 'offset-sm-3',
                       ]
                   ])->checkbox();
    echo $form->field($model, 'about')->textarea(['rows' => 3, 'maxlength' => 255]);
 ?>
    <div class="form-group">
        <div class="offset-sm-3 col-sm-9">
            <?php echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Edit'), ['class' => 'btn sf-btn']); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
