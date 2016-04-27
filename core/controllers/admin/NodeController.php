<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers\admin;

use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use app\models\Node;

class NodeController extends CommonController
{
    public function actionIndex()
    {
        $model = new Node();

        if ($model->load(Yii::$app->getRequest()->post())) {
			$node = Node::find()->select(['id','name','ename'])->where(['name'=>$model->name])->asArray()->one();
	        return $this->render('search', [
	            'model' => $model,
	            'node' => $node,
	        ]);
        } else {
			$query = Node::find();
		    $countQuery = clone $query;
		    $pages = new Pagination(['totalCount' => $countQuery->count('id')]);
		    $nodes = $query->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
		        ->limit($pages->limit)
				->asArray()
		        ->all();

	        return $this->render('index', [
	            'model' => $model,
	            'nodes' => $nodes,
	            'pages' => $pages,
	        ]);
		}
    }

    public function actionAdd()
    {
        $model = new Node();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('add', [
                'model' => $model,
            ]);
        }
    }

    public function actionEdit($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('edit', [
                'model' => $model,
            ]);
        }
    }

    protected function findModel($id)
    {
        if (($model = Node::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('未找到id为['.$id.']的节点');
        }
    }
}
