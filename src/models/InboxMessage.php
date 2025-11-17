<?php

namespace eluhr\notification\models;


use Da\User\Model\User;
use eluhr\notification\components\helpers\User as UserHelper;
use eluhr\notification\components\Notification;
use eluhr\notification\models\query\InboxMessage as InboxMessageQuery;
use Yii;
use yii\mail\MessageInterface;

/**
 * @package eluhr\notification\models
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * --- PROPERTIES ---
 *
 * @property int $id
 * @property Message $message
 * @property User $receiver
 * @property InboxMessage $next
 * @property InboxMessage $previous
 * @property bool $deleted
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
        return static::find()->hideSoftDeleted()->own()->andWhere(['<', 'id', $this->id])->orderBy(['created_at' => SORT_DESC])->one();
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
        return static::find()->hideSoftDeleted()->own()->andWhere(['>', 'id', $this->id])->orderBy(['created_at' => SORT_ASC])->one();
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
        $rules['deleted-bool'] = [
            'deleted',
            'boolean',
        ];
        $rules['deleted-default'] = [
            'deleted',
            'default',
            'value' => false
        ];
        return $rules;
    }

    public function beforeSave($insert)
    {
        if ($insert && UserHelper::preferences($this->receiver_id)->wants_to_additionally_receive_messages_by_mail === 1) {
            /** @var Notification $notificationComponent */
            $notificationComponent = Yii::$app->notification;
            $message = $this->message;
            /** @var MessageInterface $mail */
            $mail = Yii::$app->{$notificationComponent->mailerComponentName}->compose()
                ->setFrom($notificationComponent->fromEmail)
                ->setReplyTo($message->author->email)
                ->setTo($this->receiver->email)
                ->setSubject($message->subject)
                ->setHtmlBody($message->text)
                ->setTextBody($message->text);

            if (!$mail->send()) {
                Yii::error("User #{$this->receiver_id} with mail {$this->receiver->email} cannot receive emails");
                Yii::$app->session->addFlash(Yii::t('notification', 'Error while receiving message by mail'));
            }
        }
        return parent::beforeSave($insert);
    }

    public function softDelete(): bool
    {
        return $this->updateAttributes([
            'deleted' => true
        ]);
    }
}
