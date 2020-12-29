<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$db_master = require __DIR__ . '/db_master.php';

Yii::setAlias('webroot', dirname(__DIR__) . '/web');
Yii::setAlias('web', dirname(__DIR__) . '/web');

$config = [
    'id' => 'basic',
	'name' => 'Shoppyland by Honey',
	//'content' => 'รับพรีสินค้าทุกชนิดจากอเมริกา ทั้งปลีก/ส่ง ซื้อเองขายเอง แม่ค้าอยู่อเมริกาจ้า รับกดจากเว็ปด้วย',
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
			'cachePath' => '@app/runtime/cache',
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
				'from' => ['service@shoppylandbyhoney.com' => 'Shoppyland by Honey'],
			],
			//comment the following array to send mail using php's mail function
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtpout.secureserver.net',
				'username' => 'shop@buchoo.com',
				'password' => '12345678',
				'port' => '80',
				//'encryption' => 'tls',
			]
			/*'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtp.office365.com',
				'username' => 'service@shoppylandbyhoney.com',
				'password' => 'Ohm#33298',
				'port' => '587',
				'encryption' => 'tls',
			]*/
			/*
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtpout.secureserver.net',
				'username' => 'billing@shoppylandbyhoney.com',
				'password' => '12345678',
				'port' => '80',
				//'encryption' => 'tls',
			]
			*/
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
		'db_master' => $db_master,
		'dbPool' => ['class' => 'app\components\DatabasePool'],
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
		'album' => [
            'class' => 'app\modules\album\Module',
        ],
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
    'params' => $params
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['76.167.207.16', '184.191.181.6', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['76.167.207.16', '184.191.181.6', '::1'],
    ];
}

return $config;
