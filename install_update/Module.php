<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\install_update;

use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface
{
	public $layout = 'main';
	public $defaultRoute = 'install';
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules([
            'install/<action:[\w\-]+>' => $this->id . '/install/<action>',
            'install' => $this->id . '/install/index',
        ], false);
    }
}
