<?php

namespace eluhr\notification\models;


use Da\User\Model\User;
use eluhr\notification\components\helpers\Permission;
use eluhr\notification\models\query\MessageUserGroup as MessageUserGroupQuery;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @package eluhr\notification\models
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * --- PROPERTIES ---
 *
 * @property int $id
 * @property int $owner_id
 * @property User $owner
 * @property string $name
 * @property string $uniqueId
 * @property User[] $receivers
 * @property User[] $_receiverIds
 * @property array $receiverIds
 */
class MessageUserGroup extends ActiveRecord
{

    const MESSAGE_USER_GROUP_ID_PREFIX = 'mug-';

    private $_receiverIds;

    /**
     * @param $receiverIds
     */
    public function setReceiverIds($receiverIds)
    {
        $this->_receiverIds = $receiverIds;
    }

    /**
     * @param array
     *
     * @return array
     */
    public function getReceiverIds()
    {
        if (empty($this->_receiverIds)) {
            return ArrayHelper::map($this->receivers,'id','id');
        }
        return $this->_receiverIds;
    }


    /**
     * @param $uniqueId
     *
     * @return bool|array
     */
    public static function receiverIdsByUniqueId($uniqueId)
    {
        if (strpos($uniqueId, static::MESSAGE_USER_GROUP_ID_PREFIX) !== false) {
            $messageUserGroupId = substr($uniqueId, strlen(static::MESSAGE_USER_GROUP_ID_PREFIX));

            $messageUserGroup = static::findOne($messageUserGroupId);
            if ($messageUserGroup !== null) {
                return ArrayHelper::map($messageUserGroup->receivers, 'id', 'id');
            }

            if ($messageUserGroupId === '0' && Yii::$app->user->can(Permission::SEND_MESSAGE_TO_EVERYONE)) {
                return ArrayHelper::map(User::find()->all(), 'id', 'id');
            }
        }
        return false;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     *
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $transaction = Yii::$app->db->beginTransaction();

        MessageUserGroupXUser::deleteAll(['message_user_group_id' => $this->id]);

        foreach ($this->receiverIds as $receiverId) {
            $messageUserGroupXUserModel = new MessageUserGroupXUser([
                'message_user_group_id' => $this->id,
                'receiver_id' => $receiverId
            ]);

            if (!$messageUserGroupXUserModel->save()) {
                $transaction->rollBack();
                $this->delete();
                Yii::$app->session->addFlash('error', Yii::t('notification', 'Error while saving: {errors}',
                    ['errors' => implode(' ', $messageUserGroupXUserModel->getErrorSummary(true))]));
            }
        }
        $transaction->commit();
    }


    /**
     * @return MessageUserGroupQuery|object|\yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(MessageUserGroupQuery::class, [static::class]);
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%message_user_group}}';
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return static::MESSAGE_USER_GROUP_ID_PREFIX . $this->id;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::class, ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getReceivers()
    {
        return $this->hasMany(User::class, ['id' => 'receiver_id'])->viaTable(MessageUserGroupXUser::tableName(),
            ['message_user_group_id' => 'id']);
    }

    /**
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['required-rule'] = [['owner_id', 'name', 'receiverIds'], 'required'];
        $rules['owner-rule'] = [
            'owner_id',
            'exist',
            'skipOnError' => false,
            'targetClass' => User::class,
            'targetAttribute' => ['owner_id' => 'id']
        ];
        $rules['string-rule'] = ['name', 'string', 'max' => 80];
        $rules['unique-rule'] = ['name', 'unique', 'targetAttribute' => ['owner_id', 'name']];
        return $rules;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        $attributeLabels['receiverIds'] = Yii::t('notification','Receivers');
        $attributeLabels['name'] = Yii::t('notification','Name');
        return $attributeLabels;
    }

}
