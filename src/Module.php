<?php

namespace eluhr\notification;


/**
 * @package eluhr\notification
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $defaultRoute = 'inbox';

    /**
     * @var int
     */
    public $inboxMaxSelectionLength = 20;

    /**
     * @var bool
     */
    public $inboxShowToggleAll = true;
}