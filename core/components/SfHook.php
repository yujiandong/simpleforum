<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\components;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class SfHook extends Component
{
    const EVENT_AFTER_PARSE = 'afterParse';
    const EVENT_AFTER_ALL_POSTS = 'afterAllPosts';

    public function bindEvents($type)
    {
        $events = ArrayHelper::getValue(Yii::$app->params, 'events.'.$type);
        if( !empty($events) && is_array($events) ) {
            foreach($events as $event) {
                if( isset($event[1]) ) {
                    $this->on($type, $event[0], $event[1]);
                } else {
                    $this->on($type, $event[0]);
                }
            }
        }
    }
}
