<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2019 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace eluhr\notification\models;


use Da\User\Model\User;
use eluhr\notification\models\query\MessagePreferences as MessagePreferencesQuery;
use Yii;

/**
 * @package eluhr\notification\models
 * @author Elias Luhr <e.luhr@herzogkommunikation.de>
 *
 * @property int $user_id
 * @property int $wants_to_additionally_receive_messages_by_mail
 * @property User $user
 */
class MessagePreferences extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%message_preferences}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['required-rule'] = [['user_id', 'wants_to_additionally_receive_messages_by_mail'], 'required'];
        $rules['user-rule'] = [
            'user_id',
            'exist',
            'skipOnError' => false,
            'targetClass' => User::class,
            'targetAttribute' => ['user_id' => 'id']
        ];
        return $rules;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        $attributeLabels['wants_to_additionally_receive_messages_by_mail'] = Yii::t('notification',
            'Wants to additionally receive messages by mail');
        return $attributeLabels;
    }

    /**
     * @return MessagePreferencesQuery|object|\yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(MessagePreferencesQuery::class, [static::class]);
    }
}
