<?php

namespace eluhr\notification\components\helpers;

use Da\User\Model\User as UserModel;
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
}
