<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\caching\DbDependency;

class Topic extends ActiveRecord
{
    const SCENARIO_ADD = 1;
    const SCENARIO_NEW = 2;
    const SCENARIO_AUTHOR_EDIT = 3;
    const SCENARIO_ADMIN_EDIT = 10;
    const SCENARIO_ADMIN_CHGNODE = 11;

    public static function tableName()
    {
        return '{{%topic}}';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_ADD] = ['title', 'tags'];
        $scenarios[self::SCENARIO_NEW] = ['title', 'node_id', 'tags'];
        $scenarios[self::SCENARIO_AUTHOR_EDIT] = ['title', 'tags'];
        $scenarios[self::SCENARIO_ADMIN_EDIT] = ['invisible', 'comment_closed', 'alltop', 'top', 'title', 'tags'];
        $scenarios[self::SCENARIO_ADMIN_CHGNODE] = ['node_id'];
        return $scenarios;
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at', 'replied_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'node_id'], 'required'],
            [['invisible', 'comment_closed', 'alltop', 'top'], 'boolean'],
            ['node_id', 'integer'],
            ['node_id', 'exist', 'targetClass' => '\app\models\Node', 'targetAttribute' => 'id', 'message' => '节点不存在'],
            ['title', 'trim'],
            ['title', 'string', 'length' => [4, 120]],
//            [['content'], 'string', 'max' => 20000],
//            ['content', 'filter', 'filter' => 'nl2br'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'node_id' => '所属节点',
            'invisible' => '隐藏主题',
            'comment_closed' => '关闭评论',
            'alltop' => '全局置顶',
            'top' => '节点置顶',
            'title' => '标题',
        ];
    }

    public function getNode()
    {
        return $this->hasOne(Node::className(), ['id' => 'node_id'])
            ->select(['id', 'ename', 'name', 'access_auth']);
    }

    public function getTopic()
    {
        return $this->hasOne(self::className(), ['id' => 'id'])
            ->select(['id', 'node_id', 'user_id', 'reply_id', 'replied_at', 'comment_count', 'alltop', 'top', 'title']);
    }

    public function getContent()
    {
        return $this->hasOne(TopicContent::className(), ['topic_id' => 'id'])
            ->select(['topic_id', 'content']);
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
            ->select(['id', 'username', 'status', 'avatar']);
    }

    public function getLastReply()
    {
        return $this->hasOne(User::className(), ['id' => 'reply_id'])
            ->select(['id', 'username']);
    }

    public function getNaviNode()
    {
        return $this->hasOne(NaviNode::className(), ['node_id' => 'node_id'])
            ->select(['id', 'node_id']);
    }

/*
    public function getTopicTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
            ->viaTable(TagTopic::tableName(), ['topic_id' => 'id']);
    }
*/

    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['topic_id' => 'id']);
    }

    public function getFavorites()
    {
        return $this->hasMany(Favorite::className(), ['target_id' => 'id'])->onCondition([Favorite::tableName().'.type'=>Favorite::TYPE_TOPIC]);
    }

    public function getAuthorFollowedBy()
    {
        return $this->hasMany(Favorite::className(), ['target_id' => 'user_id']);
    }

    public static function getRedirectUrl($tid, $position=0, $ip=1, $np=1)
    {
        $url = ['topic/view', 'id' => $tid];

        if($position > 0) {
            $url['#'] = 'reply'.$position;
            $all = Comment::find()->where(['topic_id'=>$tid])->andWhere(['<=', 'position', $position])->count('id');
            $page = ceil($all / Yii::$app->params['settings']['comment_pagesize']);
            if($page > 1) {
                $url['p'] = $page;
            }
        }
        if($ip > 1) {
            $url['ip'] = $ip;
        }
        if($np > 1) {
            $url['np'] = $np;
        }
        return $url;
    }
    public function afterDelete()
    {
        TopicContent::deleteAll(['topic_id'=> $this->id]);
        Node::updateCounterInfo('deleteTopic', $this->node_id);
        UserInfo::updateCounterInfo('deleteTopic', $this->user_id);
        $count = Comment::afterTopicDelete($this->id);
        Siteinfo::updateCountersInfo( ['topics'=>-1, 'comments'=>-$count] );
        Favorite::afterTopicDelete($this->id);
        Notice::afterTopicDelete($this->id);
        TagTopic::afterTopicDelete($this->id);
        (new History([
            'user_id' => $this->user_id,
            'action' => History::ACTION_DELETE_TOPIC,
            'target' => $this->id,
        ]))->save(false);
        return parent::afterDelete();
    }

    public static function afterCommentInsert($id, $reply_id)
    {
        return static::updateAll([
            'updated_at'=>time(),
            'replied_at'=>time(),
            'reply_id' => $reply_id,
            'comment_count'=> (new Expression('`comment_count` + 1')),
        ], ['id'=> $id]);
    }

    public static function afterCommentDelete($id)
    {
        return static::updateAll([
            'updated_at'=>time(),
            'comment_count'=> (new Expression('`comment_count` - 1')),
        ], ['id'=> $id]);
    }

    public static function updateCounterInfo($action, $id)
    {
        $upd = [
            'followTopic' => ['favorite_count'=>1],
            'unfollowTopic' => ['favorite_count'=>-1],
        ];

        if( !isset($upd[$action]) ) {
            return false;
        }
        return static::updateAllCounters($upd[$action], ['id'=>$id]);
    }

    public static function getHotTopics()
    {
        $key = 'hot-topics';
        $cache = Yii::$app->getCache();
        $settings = Yii::$app->params['settings'];

        if ( intval($settings['cache_enabled']) === 0 || ($models = $cache->get($key)) === false ) {
            $models = static::find()->innerJoinWith('node', false)->where([Node::tableName().'.invisible' => 0])
                ->select([self::tableName().'.id', 'title'])
                ->where(['>',self::tableName().'.created_at', time()-24*60*60])
                ->orderBy(['comment_count'=>SORT_DESC, 'replied_at'=>SORT_DESC])
//              ->with(['author'])
                ->limit($settings['hot_topic_num'])
                ->asArray()
                ->all();
            if ( intval($settings['cache_enabled']) !== 0 ) {
                if ($models === null) {
                    $models = [];
                }
                $cache->set($key, $models, intval($settings['cache_time'])*60);
            }
        }
        return $models;
    }

    public static function getTopicFromView($id)
    {
        $key = 'topic-'.$id;
        $cache = Yii::$app->getCache();
        $settings = Yii::$app->params['settings'];

        if ( intval($settings['cache_enabled']) === 0 || ($model = $cache->get($key)) === false) {
            $model = static::find()->where(['id'=>$id])->with(['content', 'node', 'author'])->one();
            if ( !$model ) {
                throw new \yii\web\NotFoundHttpException('未找到id为['.$id.']的主题');
            }
        }

        $model->updateCounters(['views' => 1]);
        if ( intval($settings['cache_enabled']) !== 0 ) {
            $dep = new DbDependency(['sql'=>'SELECT updated_at FROM '. self::tableName(). 'where id='.$id]);
            $cache->set($key, $model, intval($settings['cache_time'])*60, $dep);
        }
        return $model;
    }

    public static function getTopicsFromNode($node_id, $pages)
    {
        $key = 'topics-n-'. $node_id. '-p-'. $pages->getPage();
        $cache = Yii::$app->getCache();
        $settings = Yii::$app->params['settings'];

        if ( intval($settings['cache_enabled']) === 0 || ($models = $cache->get($key)) === false) {
            $models = static::find()->where(['node_id' => $node_id])
                ->select('id')
                ->orderBy(['top'=>SORT_DESC, 'replied_at'=>SORT_DESC])
                ->offset($pages->offset)
                ->with(['topic.author', 'topic.lastReply'])
                ->limit($pages->limit)
                ->asArray()
                ->all();

            if ( intval($settings['cache_enabled']) !== 0 ) {
                if ($models === null) {
                    $models = [];
                }
                $dep = new DbDependency(['sql'=>'SELECT MAX(updated_at) FROM '. self::tableName(). 'where node_id='.$node_id]);
                $cache->set($key, $models, intval($settings['cache_time'])*60, $dep);
            }
        }
        return $models;
    }

    public static function getTopicsFromIndex($pages)
    {
        $key = 'topics-index-p-'. $pages->getPage();
        $cache = Yii::$app->getCache();
        $settings = Yii::$app->params['settings'];

        if ( intval($settings['cache_enabled']) === 0 || ($models = $cache->get($key)) === false) {
            $models = static::find()->select(Topic::tableName().'.id')->innerJoinWith('node', false)->where([Node::tableName().'.invisible' => 0])->orderBy(['alltop'=>SORT_DESC, 'replied_at'=>SORT_DESC])->offset($pages->offset)
                ->with(['topic.node', 'topic.author', 'topic.lastReply'])
                ->limit($pages->limit)
                ->asArray()
                ->all();
            if ( intval($settings['cache_enabled']) !== 0 ) {
                $dep = new DbDependency(['sql'=>'SELECT MAX(updated_at) FROM '.Topic::tableName()]);
                $cache->set($key, $models, intval($settings['cache_time'])*60, $dep);
            }
        }
        return $models;
    }

    public static function getTopicsFromSearch($pages, $q)
    {
        $models = static::find()->select('id')->where(['like', 'title', $q])->orderBy(['alltop'=>SORT_DESC, 'replied_at'=>SORT_DESC])->offset($pages->offset)
            ->with(['topic.node', 'topic.author', 'topic.lastReply'])
            ->limit($pages->limit)
            ->asArray()
            ->all();
        return $models;
    }

    public static function getTopicsFromNavi($navi_id)
    {
        $key = 'topics-navi-'. $navi_id;
        $cache = Yii::$app->getCache();
        $settings = Yii::$app->params['settings'];

        if ( intval($settings['cache_enabled']) === 0 || ($models = $cache->get($key)) === false) {
            $models = static::find()->innerJoinWith(['naviNode', 'node'],false)
                ->where([NaviNode::tableName().'.navi_id' => $navi_id, Node::tableName().'.invisible' => 0])->select([self::tableName().'.id', self::tableName().'.node_id'])
                ->orderBy(['top'=>SORT_DESC, 'replied_at'=>SORT_DESC])
                ->with(['topic.author', 'topic.lastReply', 'topic.node'])
                ->limit(intval($settings['list_pagesize']))
                ->asArray()
                ->all();

            if ( intval($settings['cache_enabled']) !== 0 ) {
                if ($models === null) {
                    $models = [];
                }
                $dep = new DbDependency(['sql'=>'SELECT MAX(updated_at) FROM '. self::tableName(). ' t1, '.NaviNode::tableName().' t2 where t1.node_id = t2.node_id and t2.navi_id='.$navi_id]);
                $cache->set($key, $models, intval($settings['cache_time'])*60, $dep);
            }
        }
        return $models;
    }

}
