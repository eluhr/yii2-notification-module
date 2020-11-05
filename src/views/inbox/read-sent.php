<?php
/**
 * --- VARIABLES ---
 *
 * @var Message $messageModel
 * @var View $this
 */

use eluhr\notification\components\helpers\Permission;
use eluhr\notification\models\Message;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\web\View;

$this->beginContent(__DIR__ . '/notification-layout.php');
?>

<div class="box-body no-padding" id="message-box">

    <div class="box-header hidden-print">
        <div class="btn-group pull-right">
            <?php
            $previousMessageModel = $messageModel->previous;
            echo Html::a(FA::icon(FA::_CHEVRON_LEFT),
                $previousMessageModel ? ['read-sent', 'messageId' => $previousMessageModel->id] : '#',
                ['class' => ['btn btn-default', $previousMessageModel === null ? ' disabled' : ''], 'title' => Yii::t('notification', 'Go to previous message')]) ?>
            <?php
            $nextMessageModel = $messageModel->next;
            echo Html::a(FA::icon(FA::_CHEVRON_RIGHT),
                $nextMessageModel !== null ? ['read-sent', 'messageId' => $nextMessageModel->id] : '#',
                ['class' => ['btn btn-default', $nextMessageModel === null ? ' disabled' : ''], 'title' => Yii::t('notification', 'Go to next message')]) ?>
        </div>
    </div>
    <div class="mailbox-read-info">
        <h3><?= Html::encode($messageModel->subject) ?></h3>
        <h5><?= Yii::t('notification', 'From: {author-username}',
                ['author-username' => $messageModel->author->username]) ?></h5>
        <h5><?= Yii::t('notification', 'To: {receiver-names}', ['receiver-names' => $messageModel->receiverLabels()]) ?>
            <span class="mailbox-read-time pull-right"><?= Yii::$app->formatter->asRelativeTime($messageModel->send_at) ?></span>
        </h5>
    </div>
    <div class="mailbox-controls with-border text-center hidden-print">
        <div class="btn-group">
            <?php if (Yii::$app->user->can(Permission::COMPOSE_A_MESSAGE)): ?>
                <?= Html::a(FA::icon(FA::_REPLY), [
                    'compose',
                    'messageId' => $messageModel->id,
                    'replyTo' => $messageModel->author_id
                ], ['class' => 'btn btn-default btn-sm', 'title' => Yii::t('notification', 'Reply to message')]) ?>

                <?= Html::a(FA::icon(FA::_SHARE), [
                    'compose',
                    'messageId' => $messageModel->id,
                ], ['class' => 'btn btn-default btn-sm', 'title' => Yii::t('notification', 'Forward message')]) ?>
            <?php endif; ?>
        </div>

    </div>
    <div class="mailbox-read-message">
        <?= $messageModel->text ?>
    </div>
</div>
<?php
$this->endContent();
?>
