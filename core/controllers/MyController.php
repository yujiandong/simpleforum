<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\Pagination;
use app\models\User;
use app\models\UserInfo;
use app\models\Notice;
use app\models\Topic;
use app\models\Token;
use app\models\History;
use app\models\Favorite;
use app\components\Util;

class MyController extends AppController
{
    public function behaviors()
    {
        return [
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

    public function actionNotifications($type='sys')
    {
        if( !in_array($type, ['sys', 'sms']) ) {
            throw new NotFoundHttpException('参数不正确');
        }

        $me = Yii::$app->getUser()->getIdentity();
//        $myId = Yii::$app->getUser()->id;
        $sysCount = $me->getSystemNoticeCount();
        $smsCount = $me->getSmsCount();
        if( $type === 'sys') {
            $condition = ['<>', 'type', Notice::TYPE_MSG];
            $with = ['source', 'topic'];
            Notice::updateAll([
                'updated_at'=>time(),
                'status'=> 1,
            ], ['and', ['status'=> 0, 'target_id'=>$me->id], ['<>', 'type', Notice::TYPE_MSG]]);
        } else {
            $condition = ['type'=> Notice::TYPE_MSG];
            $with = ['source'];
            Notice::updateAll([
                'updated_at'=>time(),
                'status'=> 1,
            ], ['status'=> 0, 'target_id'=>$me->id, 'type'=>Notice::TYPE_MSG]);
        }

        $query = Notice::find()->where(['target_id'=>$me->id])->andWhere($condition);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count('id')]);
        $notices = $query->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
            ->with($with)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        return $this->render('notifications', [
             'notices' => $notices,
             'pages' => $pages,
             'sysCount'=>$sysCount,
             'smsCount'=>$smsCount,
        ]);
    }

    public function actionSettings()
    {
        return $this->render('settings');
    }

    public function actionBalance()
    {
        $myId = Yii::$app->getUser()->id;

        $query = History::find()->where(['type'=>History::TYPE_POINT, 'user_id'=>$myId]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count('id')]);
        $records = $query->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        return $this->render('balance', [
             'records' => $records,
             'pages' => $pages,
        ]);

    }

    public function actionInviteCodes()
    {
        $myId = Yii::$app->getUser()->id;

        $query = Token::find()->where(['type'=>Token::TYPE_INVITE_CODE, 'user_id'=>$myId]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count('id')]);
        $records = $query->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        return $this->render('inviteCodes', [
             'records' => $records,
             'pages' => $pages,
        ]);

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

}
