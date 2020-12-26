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
use app\models\admin\UserForm;
use app\models\admin\ChargePointForm;
use app\models\User;

class UserController extends CommonController
{

    public function actionActivate($id)
    {
        $model = $this->findUserModel($id);
		$model->status = User::STATUS_ACTIVE;
		$model->save(false);
		return $this->goBack();
    }

    public function actionIndex($status=User::STATUS_INACTIVE)
    {
        $model = new UserForm(['scenario' => UserForm::SCENARIO_SEARCH]);

        if ($model->load(Yii::$app->getRequest()->post())) {
			$user = $model->search();
	        return $this->render('search', [
	            'model' => $model,
	            'user' => $user,
	        ]);
        } else {
			$query = User::find()->where(['status'=>$status]);
		    $countQuery = clone $query;
		    $pages = new Pagination(['totalCount' => $countQuery->count('id')]);
		    $users = $query->select(['id','username'])->orderBy(['id'=>SORT_DESC])
				->offset($pages->offset)
		        ->limit($pages->limit)
				->asArray()
		        ->all();
		    return $this->render('index', [
	            'model' => $model,
		         'users' => $users,
		         'pages' => $pages,
		    ]);
		}
    }
/*
    public function actionDelete($id)
    {
        $model = $this->findUserModel($id);
		$model->delete();

        return $this->redirect('index');

    }
*/
    public function actionInfo($id)
    {
        $model = new UserForm(['scenario' => UserForm::SCENARIO_EDIT]);
        if ($model->find($id) == null) {
            throw new NotFoundHttpException(Yii::t('app', '{attribute} doesn\'t exist.', ['attribute'=>Yii::t('app', 'User')]));
        }
        if ($model->load(Yii::$app->getRequest()->post()) && $model->edit()) {
			Yii::$app->getSession()->setFlash('adminProfileOK', Yii::t('app', '{attribute} has been changed successfully.', ['attribute' => Yii::t('app/admin', 'Member\'s information')]));
        }

        return $this->render('userinfo', [
	         'user' => $model,
        ]);

    }

    public function actionResetPassword($id)
    {
        $model = new UserForm(['scenario' => UserForm::SCENARIO_RESET_PWD]);
        if ($model->find($id) == null) {
            throw new NotFoundHttpException(Yii::t('app', '{attribute} doesn\'t exist.', ['attribute'=>Yii::t('app', 'User')]));
        }
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->resetPassword()) {
			Yii::$app->getSession()->setFlash('adminPwdOK', Yii::t('app', '{attribute} has been changed successfully.', ['attribute' => Yii::t('app', 'Password')]));
		} else {
			Yii::$app->getSession()->setFlash('adminPwdNG', implode('<br />', $model->getFirstErrors()));
		}
		return $this->goBack();
    }

    public function actionCharge()
    {
        $model = new ChargePointForm();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            if ( $model->apply() ) {
                Yii::$app->getSession()->setFlash('ChargePointOK',  Yii::t('app', 'Points have been changed successfully.'));
                $model = new ChargePointForm();
            }
        }
        return $this->render('charge', [
            'model' => $model,
        ]);

    }

    protected function findUserModel($id, $with=null)
    {
		if ($with === null) {
			$model = User::findOne($id);
		} else {
			$model = User::find()->with($with)->where(['id'=>$id])->one();
		}
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', '{attribute} doesn\'t exist.', ['attribute'=>Yii::t('app', 'User')]));
        }
    }

}
