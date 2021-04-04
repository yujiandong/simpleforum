<?php

namespace yujiandong\authclient;

use yii\authclient\OAuth2;

/**
 * Gitee allows authentication via Gitee OAuth.
 *
 * In order to use Gitee OAuth you must register your application at <https://gitee.com/oauth/applications>.
 *
 * Example application configuration:
 *
 * ~~~
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'gitee' => [
 *                 'class' => 'yujiandong\authclient\Gitee',
 *                 'clientId' => 'gitee_clientId',
 *                 'clientSecret' => 'gitee_clientSecret',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ~~~
 *
 * sample: https://simpleforum.org/site/login
 *
 * @see https://gitee.com/api/v5/oauth_doc
 * @see https://gitee.com/api/v5/swagger
 *
 * @author Jiandong Yu <admin@simpleforum.org>
 */
class Gitee extends OAuth2
{

    /**
     * @inheritdoc
     */
    public $authUrl = 'https://gitee.com/oauth/authorize';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://gitee.com/oauth/token';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://gitee.com/api/v5';

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
		return $this->api("user");
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
	{
        return 'gitee';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
	{
        return 'Gitee';
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
