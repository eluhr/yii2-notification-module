<?php
$_SERVER['HOST_NAME'] = 'web';
$_SERVER['REQUEST_TIME'] = time();
$rootDir = '/app';
// For functional and unit tests
defined('APP_TYPE') or define('APP_TYPE', 'web');
return yii\helpers\ArrayHelper::merge(
    require $rootDir . '/config/main.php',
    [
        'language' => 'en',
        'components' => [
            'request' => [
                'cookieValidationKey' => 'FUNCTIONAL_TESTING'
            ]
        ]
    ]
);