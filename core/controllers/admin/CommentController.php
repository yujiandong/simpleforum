<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers\admin;

use Yii;
use yii\web\NotFoundHttpException;
use app\models\Comment;

class CommentController extends CommonController
{

    public function actionDelete($id)
    {
        $this->findCommentModel($id)->delete();

        return $this->goBack();
    }

    protected function findCommentModel($id, $with=null)
    {
		$model = Comment::find()->where(['id'=>$id]);
		if (!empty($with)) {
			$model = $model->with($with);
		}
		$model = $model->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', '{attribute} doesn\'t exist.', ['attribute'=>Yii::t('app', 'Comment')]));
        }
    }
}
