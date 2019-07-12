<?php

namespace eluhr\notification\models;

use Da\User\Model\User;
use eluhr\notification\components\helpers\Permission;
use eluhr\notification\models\query\Message as MessageQuery;
use Yii;
use eluhr\notification\components\helpers\User as UserHelper;
use yii\helpers\ArrayHelper;

/**
 * @package eluhr\notification\models
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * --- PROPERTIES ---
 *
 * @property int $id
 * @property User $author
 * @property int $author_id
 * @property string $subject
 * @property string $text
 * @property string $send_at
 * @property \yii\db\ActiveRecord|null|array $previous
 * @property \yii\db\ActiveRecord|null|array $next
 * @property int[] $receiver_ids Array of user id's
 */
class Message extends ActiveRecord
{

    public $receiver_ids;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function possibleRecipients()
    {
        $recipients = [];
        if (Yii::$app->user->can(Permission::USER_GROUPS) || Yii::$app->user->can(Permission::SEND_MESSAGE_TO_EVERYONE)) {
            $user_groups = static::possibleUserGroups();

            if (!empty($user_groups)) {
                $recipients[Yii::t('notification', 'User Groups')] = $user_groups;
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
        $message_user_group_models = MessageUserGroup::find()->andWhere(['owner_id' => Yii::$app->user->id])->all();

        $user_groups = [];

        if (Yii::$app->user->can(Permission::SEND_MESSAGE_TO_EVERYONE)) {
            $user_groups[MessageUserGroup::MESSAGE_USER_GROUP_ID_PREFIX . '0'] = Yii::t('notification', 'All Users');
        }

        foreach ($message_user_group_models as $message_user_group_model) {
            $user_groups[$message_user_group_model->uniqueId] = $message_user_group_model->name;
        }

        return $user_groups;
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
        $rules['required-rule'] = [['author_id', 'text', 'receiver_ids', 'subject'], 'required'];
        $rules['author-rule'] = [
            'author_id',
            'exist',
            'skipOnError' => false,
            'targetClass' => User::class,
            'targetAttribute' => ['author_id' => 'id']
        ];
        $rules['safe-rule'] = ['send_at', 'safe'];
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

        $receiver_ids = static::receiverIdsByPossibleRecipients($this->receiver_ids);
        foreach ($receiver_ids as $receiver_id) {
            $inbox_message_model = new InboxMessage([
                'message_id' => $this->id,
                'receiver_id' => $receiver_id,
                'read' => 0
            ]);

            if (!$inbox_message_model->save()) {
                if ($this->delete() === false) {
                    Yii::error('Error while deleting failed message with id ' . $this->id, __CLASS__);
                }
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', Yii::t('notification', 'Error while sending: {errors}',
                    ['errors' => implode(' ', $inbox_message_model->getErrorSummary(true))]));
            }
        }

        $transaction->commit();
    }

    /**
     * @param $possible_recipients
     *
     * @return array
     */
    public static function receiverIdsByPossibleRecipients($possible_recipients)
    {
        $receiver_ids = [];
        foreach ($possible_recipients as $receiver_id) {
            $message_user_group_receiver_ids = MessageUserGroup::receiverIdsByUniqueId($receiver_id);

            if (is_array($message_user_group_receiver_ids)) {
                $receiver_ids = ArrayHelper::merge($receiver_ids, $message_user_group_receiver_ids);
            } else {
                $receiver_ids[] = $receiver_id;
            }
        }

        // convert receiver ids to array and make the array unique
        return array_unique(array_map('intval', $receiver_ids));
    }
}