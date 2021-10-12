<?php

namespace eluhr\notification\components\helpers;

use Da\User\Model\User as UserModel;
use eluhr\notification\models\MessagePreferences;
use yii\helpers\ArrayHelper;

/**
 * @package eluhr\notification\components\helpers
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
class User
{
    /**
     * @return array
     */
    public static function possibleUsers()
    {
        return ArrayHelper::map(UserModel::find()->all(), 'id', 'username');
    }


    /**
     * @param $user_id
     *
     * @return MessagePreferences
     */
    public static function preferences($user_id)
    {
        $model = MessagePreferences::findOne(['user_id' => $user_id]);

        if ($model === null) {
            $model = new MessagePreferences([
                'wants_to_additionally_receive_messages_by_mail' => 1
            ]);
        }
        return $model;
    }

    /**
     * If present, replace username, name and last name placeholders with user data.
     * @param $inboxMessageModel
     * @return string
     */
    public static function concatenateMessageSenderNames($inboxMessageModel) {
        $replacement = [
            '{author-username}' => $inboxMessageModel->message->author->username,
            '{profile-name}' => $inboxMessageModel->message->author->profile->first_name ?? '',
            '{profile-last_name}' => $inboxMessageModel->message->author->profile->last_name ?? '',
        ];
        return strtr(\Yii::$app->controller->module->senderTemplate, $replacement);
    }
}
