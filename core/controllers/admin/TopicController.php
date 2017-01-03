<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers\admin;

use Yii;
use yii\web\NotFoundHttpException;
use app\models\admin\ChangeNodeForm;
use app\models\Topic;
use app\models\Node;
use app\models\History;

class TopicController extends CommonController
{
    public function actionDelete($id)
    {
        $model = $this->findTopicModel($id, ['node']);
		$model->delete();
//		TopicContent::deleteAll(['topic_id'=> $id]);
		$request = Yii::$app->getRequest();
		if ( ($np = $request->get('np')) ) {
			$url = ['topic/node', 'name'=>$model->node->ename];
			if( $np > 1) {
				$url['p'] = $np;
			}
		} else if ( ($ip = $request->get('ip', 1)) > 1 ) {
			$url = ['topic/index', 'p'=> $ip];
		} else  {
			$url = ['topic/index'];
		}

        return $this->redirect($url);
    }

    public function actionChangeNode($id)
    {
		$request = Yii::$app->getRequest();

        $model = $this->findTopicModel($id);
		$model->scenario = Topic::SCENARIO_ADMIN_CHGNODE;
		$ext['oldNode'] = $model->node->toArray();
        if ( $model->load($request->post()) && $model->save(false) ) {
			unset($model->node);
			$ext['newNode'] = $model->node->toArray();
			(new History([
				'user_id' => Yii::$app->getUser()->id,
				'action' => History::ACTION_EDIT_TOPIC,
				'action_time' => $model->updated_at,
				'target' => $model->id,
				'ext' => json_encode($ext),
			]))->save(false);
			Node::updateAllCounters(['topic_count'=>1], ['id'=>$ext['newNode']['id']]);
			Node::updateAllCounters(['topic_count'=>-1], ['id'=>$ext['oldNode']['id']]);

			$url = ['topic/view', 'id'=>$id];
			if ( ($ip = $request->get('ip',1))>1 ) {
				$url['ip'] = $ip;
			}
	        return $this->redirect($url);
        }
        return $this->render('changeNode', [
            'model' => $model,
        ]);
    }

    protected function findTopicModel($id, $with=null)
    {
		$model = Topic::find()->where(['id'=>$id]);
		if ( !empty($with) ) {
			$model = $model->with($with);
		}
		$model = $model->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('未找到id为['.$id.']的主题');
        }
    }
}
