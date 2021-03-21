<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers\admin;

use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\base\Model;
use app\models\Navi;
use app\models\NaviNode;

class NaviController extends CommonController
{
    public function actionIndex()
    {
		$query = Navi::find();
	    $countQuery = clone $query;
	    $pages = new Pagination(['totalCount' => $countQuery->count('id')]);
	    $models = $query->select(['id', 'name', 'ename', 'type'])->orderBy(['sortid'=>SORT_ASC, 'id'=>SORT_DESC])
			->offset($pages->offset)
	        ->limit($pages->limit)
			->asArray()
	        ->all();

        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    public function actionAdd()
    {
        $model = new Navi();

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

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
		NaviNode::deleteAll(['navi_id'=>$id]);
		$model->delete();
        return $this->goBack();
    }

    public function actionNodes($id)
    {
		$node = $this->findModel($id);
		$request = Yii::$app->getRequest();
		$models = [];
		if( ($datas = $request->post('NaviNode', [])) && count($datas)>0) {
		    foreach($datas as $data) {
				if(empty($data['node_id'])) {
					continue;
				} else {
					$models[$data['node_id']] = new NaviNode(['navi_id'=>$id]+$data);
				}
			}
			if ( empty($models) ) {
				NaviNode::deleteAll(['navi_id'=>$id]);
	        } else if ( Model::validateMultiple($models)) {
				NaviNode::deleteAll(['navi_id'=>$id]);
	            foreach ($models as $model) {
	                $model->save(false);
	            }
				$flag = true;
		    }
		} else {
				$flag = true;
		}

		if ($flag === true) {
			$models = NaviNode::find()->where(['navi_id'=>$id])->orderBy(['sortid'=>SORT_ASC, 'id'=>SORT_ASC])->all();
			if(!$models) {
				$models[] = new NaviNode(['navi_id'=>$id]);
			}
		}

	    return $this->render('nodes', [
			'node'=> $node,
	        'models' => $models
	    ]);
    }

    protected function findModel($id)
    {
        if (($model = Navi::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', '{attribute} doesn\'t exist.', ['attribute'=>Yii::t('app', 'Navigation')]));
        }
    }

}
