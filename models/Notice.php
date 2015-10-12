<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Notice extends ActiveRecord
{
    const TYPE_COMMENT = 1;
    const TYPE_MENTION = 2;
    const TYPE_FOLLOW_TOPIC = 3;
    const TYPE_FOLLOW_USER = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notice}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target_id', 'source_id', 'type', 'topic_id', 'position', 'notice_count', 'status'], 'integer']
        ];
    }

	public function getSource()
    {
        return $this->hasOne(User::className(), ['id' => 'source_id'])
				->select(['id', 'username', 'avatar']);
    }

	public function getTopic()
    {
        return $this->hasOne(Topic::className(), ['id' => 'topic_id'])
				->select(['id', 'title']);
    }

	private static function findMentions($text)
	{
	    if ( !preg_match_all(User::USER_MENTION_PATTERN, $text, $out, PREG_PATTERN_ORDER) ) {
			return false;
		}
	    return array_unique($out[1]);
	}

	private static function addMentions($from)
	{
		if ( !($targetNames = self::findMentions($from['text'])) ) {
			return;
		}
		unset($from['text']);
		if ( !($targets = User::find()->select('id')->where(['in', 'username', $targetNames])->asArray()->all()) ) {
			return;
		}
		foreach($targets as $target) {
			if( $target['id'] == $from['source_id']) {
				continue;
			}
			$notice = new Notice($from);
//			$notice->attributes = $from;
			$notice->target_id = $target['id'];
			$notice->save(false);
		}
	}

	private static function addComment($from)
	{
		$notice = Notice::findOne(['type'=>self::TYPE_COMMENT, 'topic_id'=>$from['topic_id'], 'status'=>0]);
		if ($notice) {
			return $notice->updateCounters(['notice_count' => 1]);
		}

		if ( !($topic = Topic::find()->select(['user_id'])->where(['id'=>$from['topic_id']])->asArray()->one()) ) {
			return false;
		}
		if ($topic['user_id'] == $from['source_id']) {
			return true;
		}
		$notice = new Notice($from);
//		$notice->attributes = $from;
		$notice->target_id = $topic['user_id'];
		return $notice->save(false);
	}

	public static function afterCommentInsert($comment)
	{
		self::addComment([
			'type' => self::TYPE_COMMENT,
			'source_id' => $comment->user_id,
			'topic_id' => $comment->topic_id,
			'position' => $comment->position,
		]);

		return self::addMentions([
			'type' => self::TYPE_MENTION,
			'text' => $comment->content,
			'source_id' => $comment->user_id,
			'topic_id' => $comment->topic_id,
			'position' => $comment->position,
		]);
	}

	public static function afterTopicInsert($topic)
	{
		return self::addMentions([
			'type' => self::TYPE_MENTION,
			'text' => $topic->content,
			'source_id' => $topic->user_id,
			'topic_id' => $topic->id,
			'position'=> 0,
		]);
	}

	public static function afterTopicDelete($topic_id)
	{
		return static::deleteAll(['type'=>[self::TYPE_COMMENT, self::TYPE_MENTION, self::TYPE_FOLLOW_TOPIC], 'target_id'=>$topic_id]);
	}

	public static function afterFollow($favorite)
	{
		$types = [
			Favorite::TYPE_TOPIC => self::TYPE_FOLLOW_TOPIC,
			Favorite::TYPE_USER => self::TYPE_FOLLOW_USER,
		];
		$notice = new Notice([
			'type' => $types[$favorite->type],
			'source_id' => $favorite->source_id,
		]);
		if ( $favorite->type == Favorite::TYPE_TOPIC) {
			$notice->topic_id = $favorite->target_id;
			$notice->target_id = $favorite->topic->user_id;
			if ($notice->source_id == $notice->target_id) {
				return true;
			}
		} else {
			$notice->target_id = $favorite->target_id;
		}
		return $notice->save(false);
	}

}
