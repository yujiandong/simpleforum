<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

class Comment extends ActiveRecord
{
    const SCENARIO_AUTHOR = 1;
    const SCENARIO_ADMIN = 10;

    public static function tableName()
    {
        return '{{%comment}}';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_AUTHOR] = ['content'];
        $scenarios[self::SCENARIO_ADMIN] = ['invisible', 'content'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            ['invisible', 'boolean'],
            ['content', 'required'],
            ['content', 'string', 'max'=>20000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'invisible' => '隐藏',
            'content' => '内容',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
       ];
    }

    public static function primaryKey()
    {
        return ['topic_id', 'position'];
    }

    public function getTopic()
    {
        return $this->hasOne(Topic::className(), ['id' => 'topic_id'])
            ->select(['id', 'created_at', 'node_id', 'user_id', 'title']);
    }

    public function getComment()
    {
        return $this->hasOne(self::className(), ['id' => 'id'])
            ->select(['id', 'created_at', 'user_id', 'position', 'invisible', 'content']);
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
            ->select(['id', 'username', 'avatar', 'status']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert === true) {
            (new History([
                'user_id' => $this->user_id,
                'action' => History::ACTION_ADD_COMMENT,
                'action_time' => $this->created_at,
                'target' => $this->id,
            ]))->save(false);
            Siteinfo::updateCounterInfo('addComment');
            UserInfo::updateCounterInfo('addComment', $this->user_id);
            Topic::afterCommentInsert($this->topic_id, $this->user_id);
            Notice::afterCommentInsert($this);
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        (new History([
            'user_id' => $this->user_id,
            'action' => History::ACTION_DELETE_COMMENT,
            'target' => $this->id,
        ]))->save(false);
        Siteinfo::updateCounterInfo('deleteComment');
        UserInfo::updateCounterInfo('deleteComment', $this->user_id);
        Topic::afterCommentDelete($this->topic_id);
        return parent::afterDelete();
    }

    public static function afterTopicDelete($topic_id)
    {
        $limit = 100;
        $offset = 0;
        while ( 1 ) {
            $comments = static::find()->select(['user_id'])->where(['topic_id'=>$topic_id])->orderBy(['position'=>SORT_ASC])->limit($limit)->offset($offset)->asArray()->all();
            if ( empty($comments) ) {
                break;
            }
            $uids = ArrayHelper::getColumn($comments, 'user_id');
            unset($comments);
            sort($uids);
            $uidCount = array_count_values($uids);
            unset($uids);
            $result = [];
            foreach($uidCount as $key=>$value) {
                $result[$value][] = $key;
            }
            foreach($result as $key=>$value) {
                UserInfo::updateAllCounters(['comment_count'=>-$key], ['user_id'=>$value]);
            }
            unset($result);
            $offset = $offset + 100;
        }
        return static::deleteAll(['topic_id'=>$topic_id]);
    }

    public static function getCommentsFromView($topic_id, $pages)
    {
        $settings = Yii::$app->params['settings'];
        $cache = Yii::$app->getCache();
        $key = 'comments-t-'.$topic_id.'-p-'. $pages->getPage();

        if ( intval($settings['cache_enabled']) === 0 || ($models = $cache->get($key)) === false ) {
            $pids = static::find()->select('position')->where(['topic_id' => $topic_id])
                ->orderBy(['position'=>SORT_ASC])
                ->offset($pages->offset)
                ->limit($pages->limit)
                ->asArray()
                ->all();

            if ( !empty($pids) ) {
                $models = static::find()->select(['id', 'position', 'user_id', 'created_at', 'invisible', 'content'])
                    ->where(['topic_id' => $topic_id, 'position'=>$pids])
                    ->orderBy(['position'=>SORT_ASC])
                    ->with(['author'])
                    ->limit($pages->limit)
                    ->asArray()
                    ->all();
            } else {
                $models = null;
            }

            if ( intval($settings['cache_enabled']) !== 0 ) {
                if ($models === null) {
                    $models = [];
                }
                $dep = new \yii\caching\DbDependency(['sql'=>'SELECT MAX(updated_at) FROM '. self::tableName(). 'where topic_id='.$topic_id]);
                $cache->set($key, $models, intval($settings['cache_time'])*60, $dep);
            }
        }
        return $models;
    }

}
