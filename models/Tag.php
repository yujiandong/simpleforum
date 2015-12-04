<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\lib\phpanalysis\Phpanalysis;

class Tag extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }

	public static function afterTopicInsert($tc)
	{
		$editor = new \app\lib\Editor(['editor'=>Yii::$app->params['settings']['editor']]);
		$content = $editor->parse($tc->content);

		$pa = new PhpAnalysis();
		$pa->SetSource($tc->topic->title. ' ' .strip_tags($content));
		$pa->resultType = 2;
		$pa->differMax  = true;
		$pa->StartAnalysis();
		$tagNames = $pa->GetFinallyKeywords(3);
		$tagNames = explode(',', strtolower($tagNames));
		$tags = static::find()->select(['id','name'])->where(['in', 'name', $tagNames])->indexBy('name')->all();
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
	}

}
