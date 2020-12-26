<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\User;
use app\models\Topic;
use app\models\Notice;
use app\models\Favorite;
use app\lib\Util;

class FavoriteController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'cancel' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionNodes()
    {
        $me = Yii::$app->getUser()->getIdentity();

        $query = Favorite::find()->where(['type'=>Favorite::TYPE_NODE, 'source_id'=>$me->id]);
//      $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $me->userInfo->favorite_node_count, 'pageSize' => $this->settings['list_pagesize'], 'pageParam'=>'p']);
        $nodes = $query->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
                ->innerJoinWith(['node'])
                ->limit($pages->limit)
                ->asArray()
                ->all();

        return $this->render('nodes', [
            'nodes' => $nodes,
            'pages' => $pages,
        ]);
    }

    public function actionTopics()
    {
        $me = Yii::$app->getUser()->getIdentity();

        $query = Favorite::find()->where(['type'=>Favorite::TYPE_TOPIC, 'source_id'=>$me->id]);
//      $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $me->userInfo->favorite_topic_count, 'pageSize' => $this->settings['list_pagesize'], 'pageParam'=>'p']);
        $topics = $query->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
                ->innerJoinWith(['topic'])
                ->with(['topic.author','topic.lastReply','topic.node'])
                ->limit($pages->limit)
                ->asArray()
                ->all();

        return $this->render('topics', [
            'topics' => $topics,
            'pages' => $pages,
        ]);
    }

    public function actionFollowing()
    {
        $query = Topic::find()->innerJoinWith('authorFollowedBy')->where([Favorite::tableName().'.source_id'=> Yii::$app->getUser()->id, Favorite::tableName().'.type'=>Favorite::TYPE_USER]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(1), 'pageSize' => $this->settings['list_pagesize'], 'pageParam'=>'p']);
        $topics = $query->select([Topic::tableName().'.id'])->orderBy([Topic::tableName().'.id'=>SORT_DESC])->offset($pages->offset)
                ->with(['topic.author','topic.node','topic.lastReply'])
                ->limit($pages->limit)
//              ->asArray()
                ->all();
        return $this->render('following', [
            'topics' => Util::convertModelToArray($topics),
            'pages' => $pages,
        ]);
    }

    public function actionAdd($type, $id)
    {
        $types = [
            'node' => Favorite::TYPE_NODE,
            'topic' => Favorite::TYPE_TOPIC,
            'user' => Favorite::TYPE_USER,
        ];

        Favorite::add([
            'type'=>$types[$type],
            'source_id'=>Yii::$app->getUser()->id,
            'target_id'=>$id,
        ]);

        return $this->goBack();
    }

    public function actionCancel($type, $id)
    {
        $types = [
            'node' => Favorite::TYPE_NODE,
            'topic' => Favorite::TYPE_TOPIC,
            'user' => Favorite::TYPE_USER,
        ];

        Favorite::cancel([
            'type'=>$types[$type],
            'source_id'=>Yii::$app->getUser()->id,
            'target_id'=>$id,
        ]);

        return $this->goBack();
    }

}
