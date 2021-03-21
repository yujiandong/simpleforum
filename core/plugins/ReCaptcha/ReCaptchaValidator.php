<?php
namespace app\plugins\ReCaptcha;

use Yii;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\validators\Validator;
use yii\base\Exception;

class ReCaptchaValidator extends Validator
{
    public $skipOnEmpty = false;

    public $secret;
    public $action;
    public $threshold = 0.5;
    public $apiSiteVerify = 'https://www.recaptcha.net/recaptcha/api/siteverify';

    public function init()
    {
        parent::init();

        if ($this->action === null) {
            $this->action = $this->generateAuthAction();
        }
    }

    protected function generateAuthAction()
    {
        return sha1(Yii::$app->request->url);
    }


    protected function getResponse($value)
    {
        $client = new Client();

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($this->apiSiteVerify)
            ->setData(['secret' => $this->secret, 'response' => $value, 'remoteip' => Yii::$app->request->userIP])
            ->send();
        if (!$response->isOk) {
            throw new Exception('Unable connection to the captcha server. Status code ' . $response->statusCode);
        }
        return $response->data;
    }

    public function validateAttribute($model, $attribute)
    {
        $response = $this->getResponse($model->$attribute);
        if ( !ArrayHelper::getValue($response, 'success', false) ||
             ArrayHelper::getValue($response, 'action', '') !== $this->action ||
             ArrayHelper::getValue($response, 'hostname', '') !== Yii::$app->request->hostName ||
             ArrayHelper::getValue($response, 'score', 0) < $this->threshold
        ) {
            $errors = ArrayHelper::getValue($response, 'error-codes', []);
            if( count($errors) !== 1 || $errors[0] !== 'timeout-or-duplicate' ) {
                $this->addError($model, $attribute, Yii::t('yii', 'The verification code is incorrect.'));
            }
        }
    }

}
