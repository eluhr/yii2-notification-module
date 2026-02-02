<?php

namespace eluhr\notification\components\helpers;

use Da\User\Model\User as UserModel;
use eluhr\notification\models\MessagePreferences;
use eluhr\notification\Module;
use Yii;
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
        $module = Yii::$app->getModule('notification');

        if ($module instanceof Module && is_callable($module->possibleUsersCallback)) {
            return call_user_func($module->possibleUsersCallback);
        }

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
     * Concatenates user name, first name and surname using the namesTemplate
     * @param $user
     * @return string
     */
    public static function concatenateUserName($user) {
        $replacement = [
            '{author-username}' => $user->username,
            '{profile-name}' => $user->profile->first_name ?? '',
            '{profile-last_name}' => $user->profile->surname ?? '',
        ];
        return strtr(\Yii::$app->controller->module->namesTemplate, $replacement);
    }
}
