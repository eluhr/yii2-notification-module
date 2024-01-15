<?php

namespace eluhr\notification\models;

use Da\User\Model\User;
use eluhr\notification\components\helpers\Permission;
use eluhr\notification\components\helpers\User as UserHelper;
use eluhr\notification\models\query\Message as MessageQuery;
use eluhr\notification\Module;
use Yii;
use yii\db\Expression;
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
 * @property mixed $inboxMessages
 * @property int[] $receiverIds Array of user id's
 */
class Message extends ActiveRecord
{
    const PRIORITY_LOW = 0;
    const PRIORITY_NORMAL = 1;
    const PRIORITY_HIGH = 2;
    const MARK_MESSAGE_AS_READ = "MARK_MESSAGE_AS_READ";
    const MARK_MESSAGE_AS_UNREAD = "MARK_MESSAGE_AS_UNREAD";
    const DELETE_MESSAGE = "DELETE_MESSAGE";
    const SUBMIT_TYPE_NAME = "SUBMIT_TYPE_NAME";
    const DELETE_SENT_MESSAGE = "DELETE_SENT_MESSAGE";

    public $receiverIds;

    public static $receiversLimit;
    public $moduleId = 'notification';


    public function init()
    {
        parent::init();

        if (empty(static::$receiversLimit)) {
            $module = Yii::$app->getModule($this->moduleId);
            if ($module instanceof  Module) {
                static::$receiversLimit = $module->inboxMaxSelectionLength;
            } else {
                Yii::error(Yii::t('notification','Module with ID "{moduleId}" is not an instance of {modelClass}', ['moduleId' => $this->moduleId,'moduleClass' => Module::class]));
            }
        }
    }

    public function beforeValidate()
    {
        $this->text = preg_replace('#<script(.*?)>(.*?)</script>#is', '',  $this->text);
        return parent::beforeValidate();
    }

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
        $receiverModels = User::findAll(['id' => ArrayHelper::getColumn($this->getInboxMessages()->asArray()->all(), 'receiver_id')]);
        $receiverNames = [];
        foreach ($receiverModels as $receiverModel) {
            $receiverNames[] = UserHelper::concatenateUserName($receiverModel);
        }
        return $receiverNames;
    }

    public function getInboxMessages()
    {
        return $this->hasMany(InboxMessage::class, ['message_id' => 'id']);
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
     * @throws \yii\base\InvalidConfigException
     * @return array
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
     * @throws \yii\base\InvalidConfigException
     * @return array
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
        $rules['receiver-limit-rule'] = [
            'receiverIds',
            'validateReceiverLimit'
        ];
        $rules['safe-rule'] = ['send_at', 'safe'];
        $rules['int-rule'] = ['priority', 'integer'];
        $rules['priority-range-rule'] = ['priority', 'in', 'range' => array_keys(static::priorities())];
        $rules['text-filter'] = [['text','subject'], HTMLPurifierFilterValidator::class];
        return $rules;
    }

    public function validateReceiverLimit()
    {
        $limit = static::$receiversLimit;
        if (!empty($limit)) {
            $receivers = static::receiverIdsByPossibleRecipients($this->receiverIds);
            if (count($receivers) > $limit) {
                $this->addError('receiverIds', Yii::t('notification','You cannot send a message to more than {limit} receivers', ['limit' => $limit]));
            }
        }
    }

    /**
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->send_at = new Expression('NOW()');
        }

        return parent::beforeSave($insert);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @return null|array|\yii\db\ActiveRecord
     */
    public function getNext()
    {
        return static::find()->own()->andWhere(['<', 'id', $this->id])->orderBy(['created_at' => SORT_DESC])->one();
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @return MessageQuery|object|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return Yii::createObject(MessageQuery::class, [static::class]);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @return null|array|\yii\db\ActiveRecord
     */
    public function getPrevious()
    {
        return static::find()->own()->andWhere(['>', 'id', $this->id])->orderBy(['created_at' => SORT_ASC])->one();
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     *
     * @throws \yii\db\Exception
     * @throws \Throwable
     * @return bool|void
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
