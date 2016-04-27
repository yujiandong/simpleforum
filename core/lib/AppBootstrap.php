<?php
namespace app\lib;

use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;

class AppBootstrap implements BootstrapInterface
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
            'Android', 'AvantGo', 'BlackBerry', 'DoCoMo', 'Fennec', 'iPod', 'iPhone', 'iPad',
            'J2ME', 'MIDP', 'NetFront', 'Nokia', 'Opera Mini', 'Opera Mobi', 'PalmOS', 'PalmSource',
            'portalmmm', 'Plucker', 'ReqwirelessWeb', 'SonyEricsson', 'Symbian', 'UP\\.Browser',
            'webOS', 'Windows CE', 'Windows Phone OS', 'Xiino'
        ];
        $pattern = '/' . implode('|', $mobiles) . '/i';
        return (bool)preg_match($pattern, $app->getRequest()->getUserAgent());
    }

}
