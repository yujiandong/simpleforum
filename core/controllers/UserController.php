<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use app\models\User;
use app\models\Topic;
use app\models\Comment;

class UserController extends AppController
{
    public function actionView($username)
    {
        $model = $this->findUserModel($username, ['userInfo', 'topics.node', 'topics.lastReply', 'comments.topic.author']);
        return $this->render('view', [
             'user' => $model,
        ]);
    }

    public function actionTopics($username)
    {
        $user = $this->findUserModel($username, ['userInfo']);
        $pages = new Pagination(['totalCount' => $user['userInfo']['topic_count'], 'pageSize' => $this->settings['list_pagesize'], 'pageParam'=>'p']);
        $topics = Topic::find()->select('id')->where(['user_id' => $user['id']])
            ->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
            ->with(['topic.node', 'topic.lastReply'])
            ->limit($pages->limit)
            ->asArray()
            ->all();

        return $this->render('topics', [
             'user' => $user,
             'topics' => $topics,
             'pages' => $pages,
        ]);
    }

    public function actionComments($username)
    {
        $user = $this->findUserModel($username, ['userInfo']);
        $pages = new Pagination(['totalCount' => $user['userInfo']['comment_count'], 'pageSize' => $this->settings['list_pagesize'], 'pageParam'=>'p']);
        $comments = Comment::find()->select(['id', 'created_at', 'topic_id', 'content', 'invisible'])->where(['user_id' => $user['id']])
            ->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
            ->with(['topic.author'])
            ->limit($pages->limit)
            ->asArray()
            ->all();

        return $this->render('comments', [
             'user' => $user,
             'comments' => $comments,
             'pages' => $pages,
        ]);
    }

    protected function findUserModel($username, $with=null)
    {
        $model = User::find()->select(['id', 'created_at', 'status', 'username', 'avatar', 'score', 'comment'])->where(['username'=>$username]);
        if ( !empty($with) ) {
            $model = $model->with($with);
        }
        $model = $model->asArray()->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('未找到['.$username.']的用户');
        }
    }

}
