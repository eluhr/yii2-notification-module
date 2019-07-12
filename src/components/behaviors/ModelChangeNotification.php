<?php


namespace eluhr\notification\components\behaviors;


use eluhr\notification\models\Message;
use Yii;
use yii\base\Behavior;
use yii\base\ErrorException;
use yii\db\ActiveRecord;

/**
 * @package eluhr\notification\components\behaviors
 * @author Elias Luhr <elias.luhr@gmail.com>
 *
 * --- PROPERTIES ---
 *
 * @property int $alternative_author_id
 * @property int $receiver_ids
 *
 * @property \eluhr\notification\components\interfaces\ModelChangeNotification $owner
 */
class ModelChangeNotification extends Behavior
{
    const EVENT_INSERT = 'insert';
    const EVENT_UPDATE = 'update';

    /**
     * If set, this value will be used as the message's author id
     */
    public $alternative_author_id;
    public $receiver_ids = [];

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }

    /**
     * @throws ErrorException
     */
    public function afterInsert()
    {
        $this->sendNotification();
    }

    /**
     * @throws ErrorException
     */
    protected function sendNotification()
    {
        $message_model = new Message([
            'subject' => $this->owner->subject(),
            'text' => $this->owner->text(),
            'author_id' => $this->alternative_author_id ?? Yii::$app->user->id,
            'receiver_ids' => Message::receiverIdsByPossibleRecipients($this->receiver_ids)
        ]);

        if (!$message_model->save()) {
            Yii::error($message_model->errors, __CLASS__);
            throw new ErrorException(Yii::t('notification', 'Error while sending notification'));
        }
    }

    /**
     * @throws ErrorException
     */
    public function afterUpdate()
    {
        $this->sendNotification();
    }
}