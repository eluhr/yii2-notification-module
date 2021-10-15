<?php

namespace eluhr\notification\components\helpers;

class Message
{
    /**
     * If present, replace username, name and last name placeholders with user data.
     * @param $MessageModel
     * @return string
     */
    public static function concatenateMessageSenderNames($MessageModel): string
    {
        $replacement = [
            '{author-username}' => $MessageModel->author->username,
            '{profile-name}' => $MessageModel->author->profile->first_name ?? '',
            '{profile-last_name}' => $MessageModel->author->profile->surname ?? '',
        ];
        return strtr(\Yii::$app->controller->module->namesTemplate, $replacement);
    }

    /**
     * If present, replace username, name and last name placeholders with user data.
     * @param $inboxMessageModel
     * @return string
     */
    public static function concatenateInboxMessageSenderNames($inboxMessageModel) {
        return self::concatenateMessageSenderNames($inboxMessageModel->message);
    }
}