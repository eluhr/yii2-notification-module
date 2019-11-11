<?php

namespace eluhr\notification\models;

use Da\User\Model\User;
use eluhr\notification\components\helpers\Permission;
use eluhr\notification\components\helpers\User as UserHelper;
use eluhr\notification\models\query\Message as MessageQuery;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @package eluhr\notification\models
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * --- PROPERTIES ---
 *
 * @property int $id
 * @property User $author
 * @property int $author_id
 * @property string $all_receiver_ids
 * @property string $subject
 * @property string $text
 * @property string $send_at
 * @property string $priority
 * @property \yii\db\ActiveRecord|null|array $previous
 * @property \yii\db\ActiveRecord|null|array $next
 * @property string $receiverNames
 * @property int[] $receiverIds Array of user id's
 */
class Message extends ActiveRecord
{
    const PRIORITY_LOW = 0;
    const PRIORITY_NORMAL = 1;
    const PRIORITY_HIGH = 2;

    public $receiverIds;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @return array
     */
    public function getReceiverNames()
    {
        $receiverModels = User::findAll(['id' => explode(',', $this->all_receiver_ids)]);
        $receiverNames = [];
        foreach ($receiverModels as $receiverModel) {
            $receiverNames[] = $receiverModel->username;
        }
        return $receiverNames;
    }

    /**
     * @return string
     */
    public function receiverLabels()
    {
        $labels = [];
        foreach ((array)$this->receiverNames as $receiverName) {
            $labels[] = Html::tag('span', $receiverName, ['class' => 'label label-primary']);
        }
        return implode(' ', $labels);
    }

    /**
     * @return array
     */
    public static function priorities()
    {
        return [
            static::PRIORITY_HIGH => Yii::t('notification', 'High priority'),
            static::PRIORITY_NORMAL => Yii::t('notification', 'Normal priority'),
            static::PRIORITY_LOW => Yii::t('notification', 'Low priority')
        ];
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function possibleRecipients()
    {
        $recipients = [];
        if (Yii::$app->user->can(Permission::USER_GROUPS) || Yii::$app->user->can(Permission::SEND_MESSAGE_TO_EVERYONE)) {
            $userGroups = static::possibleUserGroups();

            if (!empty($userGroups)) {
                $recipients[Yii::t('notification', 'User Groups')] = $userGroups;
            }
        }

        $users = UserHelper::possibleUsers();

        if (!empty($users)) {
            $recipients[Yii::t('notification', 'Users')] = $users;
        }

        return $recipients;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    protected static function possibleUserGroups()
    {
        $messageUserGroupModels = MessageUserGroup::find()->andWhere(['owner_id' => Yii::$app->user->id])->all();

        $userGroups = [];

        if (Yii::$app->user->can(Permission::SEND_MESSAGE_TO_EVERYONE)) {
            $userGroups[MessageUserGroup::MESSAGE_USER_GROUP_ID_PREFIX . '0'] = Yii::t('notification', 'All Users');
        }

        foreach ($messageUserGroupModels as $messageUserGroupModel) {
            $userGroups[$messageUserGroupModel->uniqueId] = $messageUserGroupModel->name;
        }

        return $userGroups;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['required-rule'] = [['author_id', 'text', 'receiverIds', 'subject'], 'required'];
        $rules['author-rule'] = [
            'author_id',
            'exist',
            'skipOnError' => false,
            'targetClass' => User::class,
            'targetAttribute' => ['author_id' => 'id']
        ];
        $rules['safe-rule'] = ['send_at', 'safe'];
        $rules['int-rule'] = ['priority', 'integer'];
        return $rules;
    }

    /**
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->send_at = date('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
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
     * @return MessageQuery|object|\yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(MessageQuery::class, [static::class]);
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
     * @param bool $insert
     * @param array $changedAttributes
     *
     * @return bool|void
     * @throws \yii\db\Exception
     * @throws \Throwable
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $transaction = Yii::$app->db->beginTransaction();

        $receiverIds = static::receiverIdsByPossibleRecipients($this->receiverIds);
        foreach ($receiverIds as $receiverId) {
            $inboxMessageModel = new InboxMessage([
                'message_id' => $this->id,
                'receiver_id' => $receiverId,
                'read' => 0
            ]);

            if (!$inboxMessageModel->save()) {
                if ($this->delete() === false) {
                    Yii::error('Error while deleting failed message with id ' . $this->id, __CLASS__);
                }
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', Yii::t('notification', 'Error while sending: {errors}',
                    ['errors' => implode(' ', $inboxMessageModel->getErrorSummary(true))]));
            }
        }

        $transaction->commit();
    }

    /**
     * @param $possibleRecipients
     *
     * @return array
     */
    public static function receiverIdsByPossibleRecipients($possibleRecipients)
    {
        $receiverIds = [];
        foreach ((array)$possibleRecipients as $receiverId) {
            $messageUserGroupReceiverIds = MessageUserGroup::receiverIdsByUniqueId($receiverId);

            if (is_array($messageUserGroupReceiverIds)) {
                $receiverIds = ArrayHelper::merge($receiverIds, $messageUserGroupReceiverIds);
            } else {
                $receiverIds[] = $receiverId;
            }
        }
        // convert receiver ids to array and make the array unique
        return array_unique(array_map('intval', $receiverIds));
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        $attributeLabels['receiverIds'] = Yii::t('notification', 'Receivers');
        $attributeLabels['subject'] = Yii::t('notification', 'Subject');
        $attributeLabels['text'] = Yii::t('notification', 'Message');
        return $attributeLabels;
    }
}
