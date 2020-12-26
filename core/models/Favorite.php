<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%favorite}}".
 *
 * @property integer $id
 * @property integer $source_id
 * @property integer $type
 * @property integer $target_id
 */
class Favorite extends \yii\db\ActiveRecord
{
    const TYPE_NODE = 1;
    const TYPE_TOPIC = 2;
    const TYPE_USER = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%favorite}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source_id', 'type', 'target_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source_id' => 'Source ID',
            'type' => 'Type',
            'target_id' => 'Target ID',
        ];
    }

	public function getNode()
    {
        return $this->hasOne(Node::className(), ['id' => 'target_id'])
			->select(['id', 'ename', 'name']);
    }

	public function getTopic()
    {
        return $this->hasOne(Topic::className(), ['id' => 'target_id'])
			->select(['id', 'node_id', 'user_id', 'reply_id', 'title', 'comment_count', 'replied_at', 'comment_closed']);
    }

	public function getFollowingTopic()
    {
        return $this->hasOne(Topic::className(), ['user_id' => 'target_id']);
    }

	public function getFollowing()
    {
        return $this->hasOne(User::className(), ['id' => 'target_id']);
    }

	public static function add($favorite)
	{
		if ( !self::checkFollow($favorite['source_id'], $favorite['type'], $favorite['target_id']) ) {
			$model = new Favorite();
			$model->attributes = $favorite;
			$model->save(false);
		}
	}

	public static function cancel($favorite)
	{
		$model = Favorite::findOne($favorite);
		$model && $model->delete();
	}

	public function afterSave($insert, $changedAttributes)
	{
		if ($insert === true) {
			if ($this->type === self::TYPE_NODE) {
				$action = 'followNode';
				Node::updateCounterInfo($action, $this->target_id);
			} else if ($this->type === self::TYPE_TOPIC) {
				$action = 'followTopic';
				Topic::updateCounterInfo($action, $this->target_id);
				Notice::afterFollow($this);
			} else if ($this->type === self::TYPE_USER) {
				$action = 'followUser';
				UserInfo::updateCounterInfo('followed', $this->target_id);
				Notice::afterFollow($this);
			}
			UserInfo::updateCounterInfo($action, Yii::$app->getUser()->getId());
		}
		return parent::afterSave($insert, $changedAttributes);
	}

	public function afterDelete()
	{
		if ($this->type === self::TYPE_NODE) {
			$action = 'unfollowNode';
			Node::updateCounterInfo($action, $this->target_id);
		} else if ($this->type === self::TYPE_TOPIC) {
			$action = 'unfollowTopic';
			Topic::updateCounterInfo($action, $this->target_id);
		} else if ($this->type === self::TYPE_USER) {
			$action = 'unfollowUser';
			UserInfo::updateCounterInfo('unfollowed', $this->target_id);
		}
		UserInfo::updateCounterInfo($action, Yii::$app->getUser()->getId());
		return parent::afterDelete();
	}

	public static function afterTopicDelete($topic_id)
	{
		$limit = 100;
		$offset = 0;
		while ( 1 ) {
			$models = static::find()->select(['source_id'])->where(['type'=>self::TYPE_TOPIC, 'target_id'=>$topic_id])->orderBy(['id'=>SORT_ASC])->limit($limit)->offset($offset)->asArray()->all();
			if ( empty($models) ) {
				break;
			}
			$uids = ArrayHelper::getColumn($models, 'source_id');
			sort($uids);
			UserInfo::updateAllCounters(['favorite_topic_count'=>-1], ['user_id'=>$uids]);
			$offset = $offset + 100;
		}
		return static::deleteAll(['type'=>self::TYPE_TOPIC, 'target_id'=>$topic_id]);
	}

	public static function checkFollow($source_id, $type, $target_id)
	{
		return (static::find()->select(['id'])->where(compact('source_id', 'type', 'target_id'))->limit(1)->one() != false);
	}
}
