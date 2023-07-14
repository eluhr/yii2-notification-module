<?php

namespace eluhr\notification\models;

use Da\User\Model\User;
use Yii;

/**
 * @property int $recipient_id
 * @property int recipient_group_id
 * @property int message_id
 * @property boolean is_read
 */
class MessageRecipient extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message_recipient}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [
            [
                'message_id',
                'is_read'
            ],
            'required'
        ];
        $rules[] = [
            'is_read',
            'boolean'
        ];
        $rules[] = [
            'recipient_id',
            'exist',
            'skipOnError' => false,
            'targetClass' => User::class,
            'targetAttribute' => ['recipient_id' => 'id']
        ];
        $rules[] = [
            'recipient_group_id',
            'exist',
            'skipOnError' => false,
            'targetClass' => MessageUserGroup::class,
            'targetAttribute' => ['recipient_group_id' => 'id']
        ];
        $rules[] = [
            'message_id',
            'exist',
            'skipOnError' => false,
            'targetClass' => Message::class,
            'targetAttribute' => ['message_id' => 'id']
        ];
        $rules[] = [
            'recipient_id',
            'either',
            'params' => [
                'other' => 'recipient_group_id'
            ]
        ];
        return $rules;
    }

    /**
     * Validator to check if either on or the other field is filled.
     *
     * @param string $attribute
     * @param array $params
     *
     * @return void
     */
    public function either($attribute, $params)
    {
        if (empty($this->$attribute) && empty($this->$params['other'])) {
            $field1 = $this->getAttributeLabel($attribute);
            $field2 = $this->getAttributeLabel($params['other']);
            $this->addError($attribute, Yii::t('notification', "Either $field1 or $field2 is required."));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        $attributeLabels['recipient_id'] = Yii::t('notification', 'Recipient ID');
        $attributeLabels['recipient_group_id'] = Yii::t('notification', 'Recipient Group ID');
        $attributeLabels['message_id'] = Yii::t('notification', 'Message ID');
        $attributeLabels['is_read'] = Yii::t('notification', 'Is Read');
        return $attributeLabels;
    }
}
