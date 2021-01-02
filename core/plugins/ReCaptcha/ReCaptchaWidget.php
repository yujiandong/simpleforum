<?php
namespace app\plugins\ReCaptcha;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\InputWidget;

class ReCaptchaWidget extends InputWidget
{
    public $siteKey;
    public $jsApiUrl = 'https://www.recaptcha.net/recaptcha/api.js';
    public $action;
    public $jsCallback;

    public function __construct($siteKey = null, $action = null, $config = [])
    {
        if ($siteKey && !$this->siteKey) {
            $this->siteKey = $siteKey;
        }

        if ($action && !$this->action) {
            $this->action = $action;
        }

        parent::__construct($config);
    }
    
    public function init() {
        parent::init();
        if (empty($this->action)) {
            $this->action = $this->generateAuthAction();
        }
    }

    protected function generateAuthAction()
    {
        return sha1(Yii::$app->request->url);
    }

    public function run()
    {
        parent::run();
        $view = $this->view;

        $view->registerJsFile(
            $this->jsApiUrl . '?' . \http_build_query(['render' => $this->siteKey]),
            ['position' => $view::POS_END]
        );
        $view->registerJs(
            <<<JS
"use strict";
grecaptcha.ready(function() {
    grecaptcha.execute("{$this->siteKey}", {action: "{$this->action}"}).then(function(token) {
        jQuery("#" + "{$this->options['id']}").val(token);
    });
});
JS
            , $view::POS_READY);

        echo self::renderInputHtml('hidden');
    }
}
