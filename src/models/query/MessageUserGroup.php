<?php


namespace eluhr\notification\models\query;


use Yii;
use yii\db\ActiveQuery;

/**
 * @package eluhr\notification\models\search
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * @see \eluhr\notification\models\MessageUserGroup
 */
class MessageUserGroup extends ActiveQuery
{
    /**
     * @return MessageUserGroup
     */
    public function own()
    {
        return $this->andWhere(['owner_id' => Yii::$app->user->id]);
    }
}