Yii Authclient for Weibo,QQ,Wechat

**Config Setting**

```
'components' => [
    'authClientCollection' => [
        'class' => 'yii\authclient\Collection',
        'clients' => [
            'weibo' => [
                'class' => 'yujiandong\authclient\Weibo',
                'clientId' => 'wb_key',
                'clientSecret' => 'wb_secret',
            ],
            'qq' => [
                'class' => 'yujiandong\authclient\Qq',
                'clientId' => 'qq_appid',
                'clientSecret' => 'qq_appkey',
            ],
            'weixin' => [
                'class' => 'yujiandong\authclient\Weixin',
                'clientId' => 'weixin_appid',
                'clientSecret' => 'weixin_appkey',
            ],
        ],
    ]
    // other components
]
```
