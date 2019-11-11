<?php

namespace eluhr\notification\assets;


use yii\web\AssetBundle;

/**
 * @package eluhr\notification\assets
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
class BackendNotificationAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/web/backend';

    public $js = [
        'js/filter-form.js',
        'js/print-message.js'
    ];

    public $depends = [
        NotificationAsset::class
    ];
}
