<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

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

    public function rules()
    {
        return [
			[['website', 'about'], 'trim'],
            ['website', 'url', 'defaultScheme' => 'http'],
            ['website', 'string', 'max' => 100],
            ['about', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'website' => '个人网站',
            'about' => '个人简介',
        ];
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
			'followUser' => ['favorite_user_count'=>1],
			'unfollowUser' => ['favorite_user_count'=>-1],
			'followed' => ['favorite_count'=>1],
			'unfollowed' => ['favorite_count'=>-1],
		];

		if( !isset($upd[$action]) ) {
			return false;
		}
		return static::updateAllCounters($upd[$action], ['user_id'=>$user_id]);
	}

}
