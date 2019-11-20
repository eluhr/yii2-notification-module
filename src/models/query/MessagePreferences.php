<?php


namespace eluhr\notification\models\query;


use Yii;
use yii\db\ActiveQuery;

/**
 * @package eluhr\notification\models\search
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * @see \eluhr\notification\models\InboxMessage
 */
class MessagePreferences extends ActiveQuery
{
    /**
     * @return MessagePreferences
     */
    public function own()
    {
        return $this->andWhere(['user_id' => Yii::$app->user->id]);
    }
}
