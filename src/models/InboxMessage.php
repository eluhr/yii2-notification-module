<?php

namespace eluhr\notification\models;


use Da\User\Model\User;
use eluhr\notification\models\query\InboxMessage as InboxMessageQuery;
use Yii;

/**
 * @package eluhr\notification\models
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * --- PROPERTIES ---
 *
 * @property int $id
 * @property Message $message
 * @property int $messageId
 * @property User $receiver
 * @property int $receiverId
 * @property int $marked
 * @property InboxMessage $next
 * @property InboxMessage $previous
 * @property string $read
 *
 */
class InboxMessage extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%inbox_message}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiver()
    {
        return $this->hasOne(User::class, ['id' => 'receiver_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessage()
    {
        return $this->hasOne(Message::class, ['id' => 'message_id']);
    }

    /**
     * @return null|array|\yii\db\ActiveRecord
     * @throws \yii\base\InvalidConfigException
     */
    public function getNext()
    {
        return static::find()->own()->andWhere(['<', 'id', $this->id])->orderBy(['created_at' => SORT_DESC])->one();
    }

    /**
     * @return InboxMessageQuery|object|\yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(InboxMessageQuery::class, [static::class]);
    }

    /**
     * @return null|array|\yii\db\ActiveRecord
     * @throws \yii\base\InvalidConfigException
     */
    public function getPrevious()
    {
        return static::find()->own()->andWhere(['>', 'id', $this->id])->orderBy(['created_at' => SORT_ASC])->one();
    }

    /**
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['required-rule'] = [['receiver_id', 'message_id'], 'required'];
        $rules['message-rule'] = [
            'message_id',
            'exist',
            'skipOnError' => false,
            'targetClass' => Message::class,
            'targetAttribute' => ['message_id' => 'id']
        ];
        $rules['receiver-rule'] = [
            'receiver_id',
            'exist',
            'skipOnError' => false,
            'targetClass' => User::class,
            'targetAttribute' => ['receiver_id' => 'id']
        ];
        $rules['safe-rule'] = ['read', 'safe'];
        return $rules;
    }
}
