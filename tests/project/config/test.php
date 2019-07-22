<?php

use Da\User\Model\User as UserIdentity;
use Da\User\Module as UserModule;
use eluhr\notification\Module as NotificationModule;
use yii\console\controllers\MigrateController;
use yii\db\Connection;
use yii\i18n\PhpMessageSource;
use yii\web\User as WebUser;

Yii::$classMap[NotificationModule::class] = '/repo/src/Module.php';
Yii::$classMap[MysqlController::class] = '/repo/tests/project/controllers/MysqlController.php';

return [
    'aliases' => [
        'eluhr/notification' => '/repo/src'
    ],
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => getenv('DATABASE_DSN'),
            'username' => getenv('DATABASE_USER'),
            'password' => getenv('DATABASE_PASSWORD'),
            'charset' => 'utf8',
            'tablePrefix' => getenv('DATABASE_TABLE_PREFIX'),
            'enableSchemaCache' => YII_ENV_PROD,
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => PhpMessageSource::class,
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true
        ],
        'user' => [
            'class' => WebUser::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['/user/security/login'],
            'identityClass' => UserIdentity::class
        ],
    ],
    'controllerMap' => [
        'db' => MysqlController::class,
        'migrate' => [
            'class' => MigrateController::class,
            'migrationPath' => [
                '@yii/rbac/migrations',
                '@eluhr/notification/migrations',
                '/repo/tests/migrations'
            ],
            'migrationNamespaces' => [
                'Da\User\Migration',
            ]
        ],
    ],
    'modules' => [
        'notification' => [
            'class' => NotificationModule::class
        ],
        'user' => [
            'class' => UserModule::class
        ],
    ],
    'vendorPath' => '/repo/tests/project/vendor'
];