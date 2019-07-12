<?php

namespace eluhr\notification\assets;


use dmstr\web\AdminLteAsset;
use rmrevin\yii\fontawesome\AssetBundle as FontAwesomeAssetBundle;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * @package eluhr\notification\assets
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
class NotificationAsset extends AssetBundle
{
    public $depends = [
        AdminLteAsset::class,
        YiiAsset::class,
        FontAwesomeAssetBundle::class
    ];
}