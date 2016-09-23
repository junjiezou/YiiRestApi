<?php
$params = array_merge(
    require(__DIR__ . '/params.php')
);

$config = [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'], 	// 初始化时需要实例化的组件
	'defaultRoute'=>'index',	// 可以在index中打印API接口说明和对接说明
	'catchAll'=>null, 	 		// 启用网站维护,屏蔽所有请求，null表示不启用
	//'catchAll'=>['site/maintain'],
    'components' => [
        'request' => [
        	'parsers' => [
        		'application/json' => 'yii\web\JsonParser',
        	],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'fileCache'=>[
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
			'identityClass' => 'app\models\User',
			'enableAutoLogin' => false,
			'enableSession' => false,
        ],
        /*'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
             //send all mails to a file by default. You have to set
             //'useFileTransport' to false and configure a transport
             //for the mailer to send real emails.
            'useFileTransport' => true,
		],*/
        'log' => [
            'traceLevel' => YII_DEBUG ? 2 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'enableStrictParsing' => true,
			'rules' => [
				[
					'class' => 'yii\rest\UrlRule',
					'controller' => ['member','index','signin','feedback','version'],
					'extraPatterns' => [
							'GET login' => 'login',
					],
				],
			],
		],
/*
		'redis'=>[
			'class'=>'yii\redis\Cache',
			'keyPrefix'=>'pc#',
			'redis' => [
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 0,
            ]
		],
		'memcache'=>[
			'class' => 'yii\caching\MemCache',
            'servers' => [
                [
                    'host' => 'localhost',
                    'port' => 11211,
                    //'weight' => 60,
                ],
                [
                    'host' => 'server2',
                    'port' => 11211,
                    'weight' => 40,
                ],
            ],
		],
*/
    ],
    'params' => $params,
];
return $config;
