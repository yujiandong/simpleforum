<?php
namespace app\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;

class SfBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        self::setMobileTheme($app);
    }
    // set mobile theme
    protected function setMobileTheme($app)
    {
        $mobile = ArrayHelper::getValue($app->params, 'settings.theme_mobile', '');
        if ( !empty($mobile) && self::isMobile($app) ) {
            $themePath = '@app/themes/'.$mobile;
            $theme = ArrayHelper::getValue($app->params, 'settings.theme', '');
            if ( !empty($theme) ) {
                $themePath = [$themePath, '@app/themes/'.$theme];
            }
            $view = $app->get('view');
            if ( $view->theme ) {
                $view->theme->pathMap = ['@app/views' => $themePath];
            } else {
                $view->theme = Yii::createObject([
                  'class' => 'yii\base\Theme',
                  'pathMap'=>[
                         '@app/views' => $themePath
                  ]
              ]);
            }

            $app->set('view', $view);
        }
    }
    // check mobile device
    protected function isMobile($app) {
        $mobiles = [
            'AvantGo', 'BlackBerry', 'DoCoMo', 'Fennec', 'iPod', 'iPhone',
            'J2ME', 'MIDP', 'NetFront', 'Nokia', 'Opera Mini', 'Opera Mobi', 'PalmOS', 'PalmSource',
            'portalmmm', 'Plucker', 'ReqwirelessWeb', 'SonyEricsson', 'Symbian', 'UP\\.Browser',
            'webOS', 'Windows CE', 'Windows Phone OS', 'Xiino'
        ];
        $userAgent = $app->getRequest()->getUserAgent();
        $pattern = '/' . implode('|', $mobiles) . '/i';
        return (bool)preg_match($pattern, $userAgent) || strpos($userAgent, 'Android') !== false && strpos($userAgent, 'Mobile') !== false;
    }

    // check tablet device
    protected function isTablet($app) {
        $userAgent = $app->getRequest()->getUserAgent();
        return (bool)preg_match('/iPad/i', $userAgent) || strpos($userAgent, 'Android') !== false && strpos($userAgent, 'Mobile') === false;
    }
}
