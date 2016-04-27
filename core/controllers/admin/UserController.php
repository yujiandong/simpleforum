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
use app\models\admin\UserForm;
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
		$query = User::find()->where(['status'=>$status]);
	    $countQuery = clone $query;
	    $pages = new Pagination(['totalCount' => $countQuery->count('id')]);
	    $users = $query->select(['id','username'])->orderBy(['id'=>SORT_DESC])
			->offset($pages->offset)
	        ->limit($pages->limit)
			->asArray()
	        ->all();
	    return $this->render('index', [
	         'users' => $users,
	         'pages' => $pages,
	    ]);
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
            throw new NotFoundHttpException('未找到id为['.$id.']的用户');
        }
        if ($model->load(Yii::$app->getRequest()->post()) && $model->edit()) {
			Yii::$app->getSession()->setFlash('adminProfileOK', '用户信息修改成功');
        }

        return $this->render('userinfo', [
	         'user' => $model,
        ]);

    }

    public function actionResetPassword($id)
    {
        $model = new UserForm(['scenario' => UserForm::SCENARIO_RESET_PWD]);
        if ($model->find($id) == null) {
            throw new NotFoundHttpException('未找到id为['.$id.']的用户');
        }
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->resetPassword()) {
			Yii::$app->getSession()->setFlash('adminPwdOK', '用户密码修改成功');
		} else {
			Yii::$app->getSession()->setFlash('adminPwdNG', implode('<br />', $model->getFirstErrors()));
		}
		return $this->goBack();
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
            throw new NotFoundHttpException('未找到id为['.$id.']的用户');
        }
    }

}
