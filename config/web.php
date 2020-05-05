<?php

use auth\events\LoginEvent;
use crud\components\Crud;

$params = require __DIR__.'/params.php';
$db = require __DIR__.'/db.php';

$config = [
    'id'         => 'basic',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log', 'auth', 'crud', 'importer', 'wizard'],
    'aliases'    => [
        '@bower'    => '@vendor/bower-asset',
        '@npm'      => '@vendor/npm-asset',
        '@auth'     => '@app/modules/auth',
        '@crud'     => '@app/modules/crud',
        '@importer' => '@app/modules/importer',
        '@wizard' => '@app/modules/wizard',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'aVNz7PHS04b4I4y2rnqtCf7D230uE44r',
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'user'         => [
            'identityClass'   => 'auth\models\User',
            'on afterLogin'   => [LoginEvent::class, 'handlerAfterLogin'],
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'mailer' => [
            'class'     => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => $_ENV['MAIL_HOST'],
                'username'   => $_ENV['MAIL_USER'],
                'password'   => $_ENV['MAIL_PASSWORD'],
                'port'       => $_ENV['MAIL_PORT'],
                'encryption' => 'ssl',
            ],
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'  => $db,

        'authManager' => [
            'class'        => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [],
        ],

        'crud' => [
            'class'      => Crud::class,
            'configFile' => __DIR__.'/crud/crud.php',
        ],
    ],
    'modules'    => [
        'auth'     => [
            'class' => 'auth\Module',
        ],
        'crud'     => [
            'class' => 'crud\Module',
        ],
        'importer' => [
            'class' => 'importer\Module',
        ],
        'wizard' => [
            'class' => 'wizard\Module',
        ],
        'rbac'     => [
            'class' => 'yii2mod\rbac\Module',
        ],
    ],
    'params'     => $params,
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
        'class'      => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
