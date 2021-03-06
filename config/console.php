<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$db_master = require __DIR__ . '/db_master.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
			'class' => 'yii\web\User',
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
		'emailReader' => [
            'class' => 'app\components\EmailReader',
        ],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@app/mail',
			'messageConfig' => [
				'charset' => 'UTF-8',
				'from' => ['service@shoppylandbyhoney.com' => 'ShoppyLand by Honey'],
			],
			//comment the following array to send mail using php's mail function
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtpout.secureserver.net',
				'username' => 'service@shoppylandbyhoney.com',
				'password' => '12345678',
				'port' => '80',
				//'encryption' => 'tls',
			]
		],
        'log' => [
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
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
