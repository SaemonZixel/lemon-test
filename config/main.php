<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'common\bootstrap\SetUp', 'debug'],
	'modules' => [
		'debug' => [
			'class' => 'yii\debug\Module',
			'allowedIPs' => ['*']
		]
	],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'request' => [
			'cookieValidationKey' => 'abc123',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'formatters' => [
                'json' => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\auth\Identity',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                /* [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'categories' => ['tokenDebug'],
                    'logFile' => '@runtime/logs/tokenDebug.log',
                ],*/
                [
					'class' => 'app\components\lib\SMSAndEMailTarget',
					'levels' => ['error', 'warning'],
					'exportInterval' => 1
                ]
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
//                 '' => 'site/index',
                'GET company/list' => 'company/list',
                'GET company/<id:\d+>' => 'company/view',
                'POST company/create/<id:\d+>' => 'company/create.',
                'POST company/update/<id:\d+>' => 'company/update',
                'DELETE company/delete/<id:\d+>' => 'company/delete',
            ],
        ],
    ],
    'params' => $params,
];
