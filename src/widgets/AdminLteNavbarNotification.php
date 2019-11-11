<?php

namespace eluhr\notification\widgets;


use eluhr\notification\assets\NotificationAsset;
use eluhr\notification\models\InboxMessage;
use yii\base\Widget;

/**
 * @package eluhr\notification\models
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
class AdminLteNavbarNotification extends Widget
{
    public $moduleId = 'notification';

    public function run()
    {
        $this->registerAssets();
        $inboxMessages = InboxMessage::find()
            ->own()
            ->unread()
            ->joinWith('message')
            ->orderBy(['send_at' => SORT_DESC])
            ->all();
        $inboxMessagesCount = count($inboxMessages);
        return $this->render('admin-lte-navbar-notification', [
            'inboxMessages' => $inboxMessages,
            'moduleId' => $this->moduleId,
            'inboxMessagesCount' => $inboxMessagesCount,
            'hasMessages' => $inboxMessagesCount > 0
        ]);
    }

    private function registerAssets()
    {
        NotificationAsset::register($this->view);
    }

}
