<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\models\Topic;

$settings = Yii::$app->params['settings'];

$editorClass = Yii::$app->params['plugins'][$settings['editor']]['class'];
$editor = new $editorClass();
$editor->registerAsset($this);
$editor->registerTagItAsset($this);

?>

<?php $form = ActiveForm::begin(); ?>
    <?php
        if( $action === 'edit' && Yii::$app->getUser()->getIdentity()->isAdmin()) {
            echo '<div class="row">',
                 '<div class="col-4 col-md-3">', $form->field($model, 'invisible')->checkbox(), '</div>',
                 '<div class="col-4 col-md-3">', $form->field($model, 'comment_closed')->checkbox(), '</div>',
                 '<div class="col-4 col-md-3">', $form->field($model, 'alltop')->checkbox(), '</div>',
                 '<div class="col-4 col-md-3">', $form->field($model, 'top')->checkbox(), '</div>',
            '</div>';
        }
    ?>
    <p><?php echo Yii::t('app', 'Topic Title'); ?></p>
    <?php echo $form->field($model, 'title')->textArea(['rows' => '4', 'maxlength'=>120])->label(false); ?>
    <p><?php echo Yii::t('app', 'Topic Content'); ?> <span class="gray">( <?php echo Yii::t('app', 'Topic content can be empty.'); ?> )</span></p>
    <?php echo $form->field($content, 'content')->textArea(['id'=>'editor', 'maxlength'=>30000])->label(false); ?>
    <p><?php echo Yii::t('app', 'Visible Option'); ?></p>
    <?php echo $form->field($model, 'access_auth')->dropDownList(Topic::accessList())->label(false); ?>
    <p><?php echo Yii::t('app', 'Tags'); ?> <span class="gray">( <?php echo Yii::t('app', 'max: 4 tags, delimiter: blank'); ?> )</span></p>
    <?php echo $form->field($model, 'tags')->textInput(['id'=>'tags', 'maxlength'=>60])->label(false); ?>
<?php
    if( Yii::$app->getUser()->getIdentity()->canUpload($settings) ) {
        $editor->registerUploadAsset($this);
        echo '<div class="form-group"><div id="fileuploader">'.Yii::t('app', 'Upload Images').'</div></div>';
    }
    $captcha = ArrayHelper::getValue(Yii::$app->params, 'settings.captcha', '');
    if(!empty($captcha) && ($plugin=ArrayHelper::getValue(Yii::$app->params, 'plugins.' . $captcha, []))) {
        $plugin['class']::captchaWidget('newtopic', $form, $model, null, $plugin);
    }
?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '<i class="fas fa-pencil-alt"></i>'.Yii::t('app', 'Post') : '<i class="fas fa-edit"></i>'.Yii::t('app', 'Edit'), ['class' => 'btn sf-btn']); ?>
    </div>
<?php ActiveForm::end(); ?>
