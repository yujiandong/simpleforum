<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers\admin;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use yii\db\Expression;
use app\models\Tag;
use app\models\Topic;

class TagController extends CommonController
{
    public function actionIndex()
    {
		$query = Tag::find();
	    $countQuery = clone $query;
	    $pages = new Pagination(['totalCount' => $countQuery->count('id')]);
	    $tags = $query->select(['id', 'created_at', 'name', 'topic_count'])->orderBy(['created_at'=>SORT_DESC, 'id'=>SORT_DESC])
			->offset($pages->offset)
	        ->limit($pages->limit)
			->asArray()
	        ->all();

        return $this->render('index', [
            'tags' => $tags,
            'pages' => $pages,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
		$model->delete();
        return $this->goBack();
    }

    public function actionEdit($id)
    {
        $model = $this->findModel($id);
		$oldTag = $model->name;
        if ($model->load(Yii::$app->getRequest()->post()) && ($model->name=strtolower($model->name)) && $model->save()) {
			Topic::afterTagEdit($model, $oldTag);
            return $this->redirect(['index']);
        } else {
            return $this->render('edit', [
                'model' => $model,
            ]);
        }
    }

    protected function findModel($id, $with=null)
    {
		$model = Tag::find()->where(['id' => $id]);
		if ( !empty($with) ) {
			$model = $model->with($with);
		}
		$model = $model->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', '{attribute} doesn\'t exist.', ['attribute'=>Yii::t('app', 'Tag')]));
        }
    }
}
