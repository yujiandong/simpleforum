<?php

namespace simpleforum\authclient;

use yii\authclient\OAuth2;

/**
 * Weibo allows authentication via Weibo OAuth.
 *
 * In order to use Weibo OAuth you must register your application at <http://open.weibo.com/>.
 *
 * Example application configuration:
 *
 * ~~~
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'qq' => [
 *                 'class' => 'simpleforum\authclient\Weibo',
 *                 'clientId' => 'wb_key',
 *                 'clientSecret' => 'wb_secret',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ~~~
 *
 * @see http://open.weibo.com/
 * @see http://open.weibo.com/wiki/
 *
 * @author Jiandong Yu <flyyjd@gmail.com>
 */
class Weibo extends OAuth2
{

    /**
     * @inheritdoc
     */
    public $authUrl = 'https://api.weibo.com/oauth2/authorize';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://api.weibo.com/oauth2/access_token';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://api.weibo.com';

    /**
     * @inheritdoc
     */
    protected function defaultNormalizeUserAttributeMap()
    {
        return [
            'username' => 'name',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
	{
        $openid = $this->api('oauth2/get_token_info', 'POST');
		return $this->api("2/users/show.json", 'GET', ['uid' => $openid['uid']]);
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
	{
        return 'weibo';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
	{
        return '”÷”Ž';
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
