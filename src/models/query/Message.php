<?php


namespace eluhr\notification\models\query;


use Yii;
use yii\db\ActiveQuery;

/**
 * @package eluhr\notification\models\search
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * @see \eluhr\notification\models\Message
 */
class Message extends ActiveQuery
{
    /**
     * @return Message
     */
    public function own()
    {
        return $this->andWhere(['author_id' => Yii::$app->user->id]);
    }
}