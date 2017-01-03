<?php

namespace yujiandong\authclient;

use yii\authclient\OAuth2;
use yii\web\HttpException;
use Yii;

/**
 * QQ allows authentication via QQ OAuth.
 *
 * In order to use QQ OAuth you must register your application at <http://connect.qq.com/>.
 *
 * Example application configuration:
 *
 * ~~~
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'qq' => [
 *                 'class' => 'yujiandong\authclient\Qq',
 *                 'clientId' => 'qq_appid',
 *                 'clientSecret' => 'qq_appkey',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ~~~
 *
 * @see http://connect.qq.com/
 * @see http://wiki.connect.qq.com/
 *
 * @author Jiandong Yu <flyyjd@gmail.com>
 */
class Qq extends OAuth2
{

    /**
     * @inheritdoc
     */
    public $authUrl = 'https://graph.qq.com/oauth2.0/authorize';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://graph.qq.com/oauth2.0/token';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://graph.qq.com';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(' ', [
                'get_user_info',
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function defaultNormalizeUserAttributeMap()
    {
        return [
            'username' => 'nickname',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        $user = $this->api('oauth2.0/me', 'GET');
        if ( isset($user['error']) ) {
            throw new HttpException(400, $user['error']. ':'. $user['error_description']);
        }
        $userAttributes = $this->api(
            "user/get_user_info",
            'GET',
            [
                'oauth_consumer_key' => $user['client_id'],
                'openid' => $user['openid'],
            ]
        );
        $userAttributes['id'] = $user['openid'];
        return $userAttributes;
    }

    /**
     * @inheritdoc
     */
    protected function sendRequest($request)
    {
        $response = $request->send();

        if (!$response->getIsOk()) {
            throw new InvalidResponseException($response, 'Request failed with code: ' . $response->getStatusCode() . ', message: ' . $response->getContent());
        }

        $content = $response->getContent();
        if (!empty($content)) {
            if (strpos($content, "callback(") === 0) {
                $count = 0;
                $jsonData = preg_replace('/^callback\(\s*(\\{.*\\})\s*\);$/is', '\1', $content, 1, $count);
                if ($count === 1) {
                    $response->setContent($jsonData);
                }
            }
        }

        return  $response->getData();
    }

    /**
     * Generates the auth state value.
     * @return string auth state value.
     */
    protected function generateAuthState()
    {
        return sha1(uniqid(get_class($this), true));
    }

    /**
     * @inheritdoc
     */
    protected function defaultReturnUrl()
    {
        $params = $_GET;
        unset($params['code']);
        unset($params['state']);
        $params[0] = Yii::$app->controller->getRoute();

        return Yii::$app->getUrlManager()->createAbsoluteUrl($params);
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'qq';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'QQ';
    }

    /**
     * @inheritdoc
     */
    protected function defaultViewOptions()
    {
        return [
            'popupWidth' => 800,
            'popupHeight' => 500,
        ];
    }
}
