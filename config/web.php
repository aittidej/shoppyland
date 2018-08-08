<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

Yii::setAlias('webroot', dirname(__DIR__) . '/web');
Yii::setAlias('web', dirname(__DIR__) . '/web');

$config = [
    'id' => 'basic',
	'name' => 'ShoppyLand by Honey',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'pemBG>MfE2~5AbC@sjUrZ_XNR&6WYFqu',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
		'passwordhash' => [
            'class' => 'app\components\PasswordHash',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
		'emailReader' => [
            'class' => 'app\components\EmailReader',
        ],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@app/mail',
			'messageConfig' => [
				'charset' => 'UTF-8',
				'from' => ['info@shoppylandbyhoney.com' => 'ShoppyLand by Honey'],
			],
			//comment the following array to send mail using php's mail function
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtpout.secureserver.net',
				'username' => 'info@shoppylandbyhoney.com',
				'password' => '12345678',
				'port' => '80',
				//'encryption' => 'tls',
			]
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'rules' => [
				// your rules go here
			],
			// ...
		],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
	'modules' => [
	   'gridview' =>  [
			'class' => '\kartik\grid\Module',
			// enter optional module parameters below - only if you need to
			// use your own export download action or custom translation
			// message source
			'downloadAction' => 'gridview/export/download',
			'i18n' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@kvgrid/messages',
					'forceTranslation' => true
				]
		],
		'dynagrid'=> [
			'class'=>'\kartik\dynagrid\Module',
			// other module settings
		],
	],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
