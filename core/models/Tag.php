<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
//use app\lib\phpanalysis\Phpanalysis;

class Tag extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag}}';
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

	public static function getTags($tags)
	{
		$tags = strtolower(trim($tags));
		$tagNames = [];
		if (!empty($tags)) {
			$tagNames = explode(',', $tags);
			$tagNames = array_unique($tagNames);
			$tagNames = array_filter($tagNames, function($item) {
				return !empty($item) && trim($item)!=='';
			});
		}
		if( !empty($tags) && !empty($tagNames) ) {
			return implode(',', $tagNames);
		}
		return '';
	}
/*
	public static function getTags($tags, $title, $content)
	{
		$tags = strtolower(trim($tags));
		$tagNames = [];
		if (!empty($tags)) {
			$tagNames = explode(',', $tags);
			$tagNames = array_unique($tagNames);
			$tagNames = array_filter($tagNames, function($item) {
				return !empty($item) && trim($item)!=='';
			});
		}
		if( !empty($tags) && !empty($tagNames) ) {
			return implode(',', $tagNames);
		}
			$editor = new \app\lib\Editor(['editor'=>Yii::$app->params['settings']['editor']]);
			$content = $editor->parse($content);

			$pa = new PhpAnalysis();
			$pa->SetSource($title . ' ' . strip_tags($content));
			$pa->resultType = 2;
			$pa->differMax  = true;
			$pa->StartAnalysis();
			$tags = $pa->GetFinallyKeywords(3);
			return strtolower($tags);
	}
	public static function editTags($newTagStr, $oldTagStr)
	{
		$newTagStr = strtolower(trim($newTagStr));
		$tagNames = [];
		if (empty($newTagStr) || $newTagStr === $oldTagStr) {
			return $oldTagStr;
		}
		$tagNames = explode(',', $newTagStr);
		$tagNames = array_unique($tagNames);
		$tagNames = array_filter($tagNames, function($item) {
			return !empty($item) && trim($item)!=='';
		});
		if(empty($tagNames)) {
			return $oldTagStr;
		}
		return implode(',', $tagNames);
	}
*/
	public static function afterTopicEdit($topicId, $newTagStr, $oldTagStr)
	{
		if($newTagStr === $oldTagStr) {
			return;
		}
		$newTags = explode(',', $newTagStr);
		$oldTags = explode(',', $oldTagStr);
		$addTags = array_diff($newTags, $oldTags);
		$deleteTags = array_diff($oldTags, $newTags);
		if ( !empty($deleteTags) ) {
			static::deleteTopicTags($topicId, $deleteTags);
		}
		if(!empty($addTags)) {
			static::addTopicTags($topicId, $addTags);
		}
	}

	public static function afterTopicInsert($tc)
	{
		$tagStr = strtolower(trim($tc->topic->tags));
		if( empty($tagStr) ) {
			return;
		}
		$tagNames = explode(',', $tagStr);
		static::addTopicTags($tc->topic_id, $tagNames);
/*		$tags = static::find()->select(['id','name'])->where(['in', 'name', $tagNames])->indexBy('name')->all();
		foreach($tagNames as $tn) {
			if ( !empty($tags) && !empty($tags[$tn])) {
				$tag = $tags[$tn];
				$tagTopic = new TagTopic(['tag_id'=>$tag->id, 'topic_id'=>$tc->topic_id]);
				$tagTopic->save(false);
				$tag->updateCounters(['topic_count' => 1]);
			} else {
				$tag = new static(['name'=>$tn, 'topic_count'=>1]);
				$tag->save(false);
				$tagTopic = new TagTopic(['tag_id'=>$tag->id, 'topic_id'=>$tc->topic_id]);
				$tagTopic->save(false);
			}
		}
*/
	}

	public static function addTopicTags($topicId, $tagNames)
	{
		$tags = static::find()->select(['id','name'])->where(['name'=>$tagNames])->indexBy('name')->all();
		foreach($tagNames as $tn) {
			if ( !empty($tags) && !empty($tags[$tn])) {
				$tag = $tags[$tn];
				$tagTopic = new TagTopic(['tag_id'=>$tag->id, 'topic_id'=>$topicId]);
				$tagTopic->save(false);
//				$tag->updateCounters(['topic_count' => 1]);
				static::updateAll([
					'updated_at'=>time(),
					'topic_count'=> (new Expression('`topic_count` + 1')),
				], ['id'=> $tag->id]);
			} else {
				$tag = new static(['name'=>$tn, 'topic_count'=>1]);
				$tag->save(false);
				$tagTopic = new TagTopic(['tag_id'=>$tag->id, 'topic_id'=>$topicId]);
				$tagTopic->save(false);
			}
		}
	}

	public static function deleteTopicTags($topicId, $tagNames)
	{
		if( ($oldTags = static::find(['id'])->where(['name'=>$tagNames])->asArray()->all()) ) {
			$tagIds = ArrayHelper::getColumn($oldTags, 'id');
			TagTopic::deleteAll(['topic_id'=>$topicId, 'tag_id'=>$tagIds]);
			static::updateAll([
				'updated_at'=>time(),
				'topic_count'=> (new Expression('`topic_count` - 1')),
			], ['and', ['id'=>$tagIds], ['>', 'topic_count', 0]]);
		}
	}

}
