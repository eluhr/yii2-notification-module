<?php

namespace eluhr\notification\models;


use Da\User\Model\User;

/**
 * @package eluhr\notification\models
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * --- PROPERTIES ---
 *
 * @property MessageUserGroup $messageUserGroup
 * @property int $message_user_group_id
 * @property User $receiver
 * @property int $receiver_id
 */
class MessageUserGroupXUser extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%message_user_group_x_user}}';
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
    public function getMessageUserGroup()
    {
        return $this->hasOne(MessageUserGroup::class, ['id' => 'message_user_group_id']);
    }

    /**
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['required-rule'] = [['receiver_id', 'message_user_group_id'], 'required'];
        $rules['user-rule'] = [
            'receiver_id',
            'exist',
            'skipOnError' => false,
            'targetClass' => User::class,
            'targetAttribute' => ['receiver_id' => 'id']
        ];
        $rules['message-user-group-rule'] = [
            'message_user_group_id',
            'exist',
            'skipOnError' => false,
            'targetClass' => MessageUserGroup::class,
            'targetAttribute' => ['message_user_group_id' => 'id']
        ];
        return $rules;
    }
}