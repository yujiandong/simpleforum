<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;

class UserInfo extends \yii\db\ActiveRecord
{
    const SCENARIO_EDIT = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_info}}';
    }

    public function scenarios()
    {
        return [self::SCENARIO_EDIT => ['website', 'about']];
    }

	public static function updateCounterInfo($action, $user_id)
	{
		$upd = [
			'addTopic' => ['topic_count'=>1],
			'deleteTopic' => ['topic_count'=>-1],
			'addComment' => ['comment_count'=>1],
			'deleteComment' => ['comment_count'=>-1],
			'followNode' => ['favorite_node_count'=>1],
			'unfollowNode' => ['favorite_node_count'=>-1],
			'followTopic' => ['favorite_topic_count'=>1],
			'unfollowTopic' => ['favorite_topic_count'=>-1],
			'followUser' => ['following_count'=>1],
			'unfollowUser' => ['following_count'=>-1],
			'followed' => ['follower_count'=>1],
			'unfollowed' => ['follower_count'=>-1],
		];

		if( !isset($upd[$action]) ) {
			return false;
		}
		return static::updateAllCounters($upd[$action], ['user_id'=>$user_id]);
	}

}
