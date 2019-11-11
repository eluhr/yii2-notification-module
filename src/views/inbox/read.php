<?php
/**
 * --- VARIABLES ---
 *
 * @var InboxMessage $inboxMessageModel
 * @var View $this
 */

use eluhr\notification\components\helpers\Permission;
use eluhr\notification\models\InboxMessage;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\web\View;

$this->beginContent(__DIR__ . '/notification-layout.php');
?>

<div class="box-body no-padding" id="message-box">

    <div class="box-header hidden-print">
        <div class="btn-group pull-right">
            <?php
            $previousInboxMessageModel = $inboxMessageModel->previous;
            echo Html::a(FA::icon(FA::_CHEVRON_LEFT),
                $previousInboxMessageModel ? ['read', 'inboxMessageId' => $previousInboxMessageModel->id] : '#',
                ['class' => 'btn btn-default' . ($previousInboxMessageModel === null ? ' disabled' : '')]) ?>
            <?php
            $nextInboxMessageModel = $inboxMessageModel->next;
            echo Html::a(FA::icon(FA::_CHEVRON_RIGHT), $nextInboxMessageModel !== null ? [
                'read',
                'inboxMessageId' => $nextInboxMessageModel->id
            ] : '#', ['class' => 'btn btn-default' . ($nextInboxMessageModel === null ? ' disabled' : '')]) ?>
        </div>
    </div>
    <div class="mailbox-read-info">
        <h3><?= $inboxMessageModel->message->subject ?></h3>
        <h5><?= Yii::t('notification', 'From: {author-username}',
                ['author-username' => $inboxMessageModel->message->author->username]) ?>
            <span class="mailbox-read-time pull-right"><?= Yii::$app->formatter->asRelativeTime($inboxMessageModel->message->send_at) ?></span>
        </h5>
    </div>
    <div class="mailbox-controls with-border text-center hidden-print">
        <div class="btn-group">
            <?= Html::a(FA::icon(FA::_PRINT), 'javascript:void(0)', [
                'class' => 'btn btn-default btn-sm',
                'data-print' => 'message-box'
            ]) ?>
        </div>
        <div class="btn-group">
            <?php if (Yii::$app->user->can(Permission::COMPOSE_A_MESSAGE)): ?>
                <?= Html::a(FA::icon(FA::_REPLY), [
                    'compose',
                    'messageId' => $inboxMessageModel->message_id,
                    'replyTo' => $inboxMessageModel->message->author_id
                ], ['class' => 'btn btn-default btn-sm']) ?>

                <?= Html::a(FA::icon(FA::_SHARE), [
                    'compose',
                    'messageId' => $inboxMessageModel->message_id,
                ], ['class' => 'btn btn-default btn-sm']) ?>
            <?php endif; ?>
        </div>
        <div class="btn-group">
            <?= Html::a(FA::icon(FA::_TRASH_O), [
                'delete-inbox-message',
                'inboxMessageId' => $inboxMessageModel->id,
            ], [
                'class' => 'btn btn-danger btn-sm',
                'data-method' => 'post',
                'data-confirm' => Yii::t('notification', 'Are you sure you want to delete this message?')
            ]) ?>
        </div>

    </div>
    <div class="mailbox-read-message">
        <?= $inboxMessageModel->message->text ?>
    </div>
</div>
<?php
$this->endContent();
?>
