<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2019 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace eluhr\notification\components\helpers;


use eluhr\notification\models\MessagePreferences;
use yii\helpers\ArrayHelper;
use Da\User\Model\User as UserModel;

/**
 * @package eluhr\notification\components\helpers
 * @author Elias Luhr <e.luhr@herzogkommunikation.de>
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
}
