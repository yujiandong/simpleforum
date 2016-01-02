<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\caching\DbDependency;
use yii\helpers\ArrayHelper;

class TagTopic extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag_topic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_id', 'topic_id'], 'integer']
        ];
    }

	public function getTopic()
    {
        return $this->hasOne(Topic::className(), ['id' => 'topic_id'])
				->select(['id', 'node_id', 'user_id', 'reply_id', 'replied_at', 'comment_count', 'title']);
    }

	public function getTag()
    {
        return $this->hasOne(Tag::className(), ['id' => 'tag_id'])
				->select(['id', 'name']);
    }

	public static function getTopics($tagId, $pages)
	{
		$key = 'topics-tag-'. $tagId .'-p-'. $pages->getPage();
		$cache = Yii::$app->getCache();
		$settings = Yii::$app->params['settings'];

		if ( intval($settings['cache_enabled']) === 0 || ($models = $cache->get($key)) === false) {
		    $models = static::find()->select(['id', 'topic_id'])->where(['tag_id'=>$tagId])
				->orderBy(['id'=>SORT_DESC])->offset($pages->offset)
				->with(['topic.node', 'topic.author', 'topic.lastReply'])
		        ->limit($pages->limit)
				->asArray()
		        ->all();
			if ( intval($settings['cache_enabled']) !== 0 ) {
				$dep = new DbDependency(['sql'=>'SELECT MAX(updated_at) FROM '. Tag::tableName(). 'where id = ' . $tagId]);
				$cache->set($key, $models, intval($settings['cache_time'])*60, $dep);
			}
		}
		return $models;
	}

	public static function afterTopicDelete($topicId)
	{
		$tagTopics = static::find()->select(['tag_id'])->where(['topic_id'=>$topicId])->asArray()->all();
		if ( empty($tagTopics) ) {
			return;
		}
		$tagIds = ArrayHelper::getColumn($tagTopics, 'tag_id');
		Tag::updateAllCounters(['topic_count'=>-1], ['and', ['id'=>$tagIds], ['>', 'topic_count', 0]]);
		static::deleteAll(['topic_id'=>$topicId]);
	}

}
