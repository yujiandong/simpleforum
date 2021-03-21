<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\install_update;

class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
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
            'upgrade/<action:[\w\-]+>' => $this->id . '/upgrade/<action>',
            'install' => $this->id . '/install/index',
        ], false);
    }
}
