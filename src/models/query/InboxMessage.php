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
class InboxMessage extends ActiveQuery
{
    /**
     * @return InboxMessage
     */
    public function own()
    {
        return $this->andWhere(['receiver_id' => Yii::$app->user->id]);
    }

    /**
     * @return InboxMessage
     */
    public function read()
    {
        return $this->andWhere(['read' => 1]);
    }

    /**
     * @return InboxMessage
     */
    public function unread()
    {
        return $this->andWhere(['read' => 0]);
    }

    public function hideSoftDeleted()
    {
        return $this->andWhere(['deleted' => false]);
    }
}
