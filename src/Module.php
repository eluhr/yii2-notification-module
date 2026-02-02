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
    public $namesTemplate = '{author-username}';

    /**
     * Show checkboxes for bulk delete and mark as read
     * @var bool
     */
    public $checkboxEnabled = false;

    /**
     * Instead of deleting a inbox message from the database, mark them as deleted
     */
    public $allowInboxMessageSoftDelete = false;

    /**
     * @var callable|null Callback to override possible users list.
     * Should return array in format: [userId => displayName]
     * If null, defaults to all users from User model.
     *
     * Example:
     * 'possibleUsersCallback' => function() {
     *     return ArrayHelper::map(User::find()->active()->all(), 'id', 'username');
     * }
     */
    public $possibleUsersCallback;
}
