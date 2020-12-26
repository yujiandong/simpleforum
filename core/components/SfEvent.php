<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\components;

use Yii;
use yii\base\Event;

class SfEvent extends Event
{
    public $output;
    public $render;
    public $options;
}
