<?php

namespace eluhr\notification\components;


use Da\User\Model\User;
use eluhr\notification\models\Message;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * @package eluhr\notification\components
 * @author Elias Luhr <e.luhr@herzogkommunikation.de>
 *
 * @property string $mailerComponentName
 * @property string $fromEmail
 */
class Notification extends Component
{
    public $mailerComponentName = 'mailer';

    public $fromEmail;

    /**
     * @param string|array $toUserIds
     * @param string $subject
     * @param string $text
     *
     * @return bool
     */
    public function sendMessage($toUserIds, $subject, $text)
    {
        $userModels = User::findAll(['id' => (array)$toUserIds]);
        if (empty($userModels)) {
            return false;
        }
        $receiverIds = ArrayHelper::getColumn($userModels, 'id');

        $messageModel = new Message([
            'subject' => $subject,
            'text' => $text,
            'author_id' => Yii::$app->user->id,
            'receiver_ids' => $receiverIds
        ]);

        return $messageModel->save();
    }
}
