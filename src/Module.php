<?php

namespace eluhr\notification;


/**
 * @package eluhr\notification
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * --- PROPERTIES ---
 *
 * @property string $defaultRoute
 * @property int|null $inboxMaxSelectionLength
 * @property bool $inboxShowToggleAll
 */
class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $defaultRoute = 'inbox';

    /**
     * If set to null, there is no limit in max receivers
     * @var int|null
     */
    public $inboxMaxSelectionLength = 20;

    /**
     * @var bool
     */
    public $inboxShowToggleAll = true;

    /**
     * @var
     */
    public $senderTemplate = 'author-username';
}