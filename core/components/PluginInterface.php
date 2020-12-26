<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\components;

interface PluginInterface
{
    public static function info();
    public static function install();
    public static function uninstall();
}
