<?php

use eluhr\notification\components\helpers\Permission;
use eluhr\notification\models\InboxMessage;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\web\View;

/**
 * --- VARIABLES ---
 *
 * @var InboxMessage $inbox_message_model
 * @var View $this
 */

$this->beginContent(__DIR__ . '/notification-layout.php');
?>

<div class="box-body no-padding" id="message-box">

    <div class="box-header hidden-print">
        <div class="btn-group pull-right">
            <?php
            $previous_inbox_message_model = $inbox_message_model->previous;
            echo Html::a(FA::icon(FA::_CHEVRON_LEFT),
                $previous_inbox_message_model ? ['read', 'inbox_message_id' => $previous_inbox_message_model->id] : '#',
                ['class' => 'btn btn-default' . ($previous_inbox_message_model === null ? ' disabled' : '')]) ?>
            <?php
            $next_inbox_message_model = $inbox_message_model->next;
            echo Html::a(FA::icon(FA::_CHEVRON_RIGHT), $next_inbox_message_model !== null ? [
                'read',
                'inbox_message_id' => $next_inbox_message_model->id
            ] : '#', ['class' => 'btn btn-default' . ($next_inbox_message_model === null ? ' disabled' : '')]) ?>
        </div>
    </div>
    <div class="mailbox-read-info">
        <h3><?= $inbox_message_model->message->subject ?></h3>
        <h5><?= Yii::t('notification', 'From: {author-username}',
                ['author-username' => $inbox_message_model->message->author->username]) ?>
            <span class="mailbox-read-time pull-right"><?= Yii::$app->formatter->asRelativeTime($inbox_message_model->message->send_at) ?></span>
        </h5>
    </div>
    <div class="mailbox-controls with-border text-center hidden-print">
        <div class="btn-group">
            <?php if (Yii::$app->user->can(Permission::COMPOSE_A_MESSAGE)): ?>
                <?= Html::a(FA::icon(FA::_REPLY), [
                    'compose',
                    'message_id' => $inbox_message_model->message_id,
                    'reply_to' => $inbox_message_model->message->author_id
                ], ['class' => 'btn btn-default btn-sm']) ?>

                <?= Html::a(FA::icon(FA::_SHARE), [
                    'compose',
                    'message_id' => $inbox_message_model->message_id,
                ], ['class' => 'btn btn-default btn-sm']) ?>
            <?php endif; ?>
        </div>
        <div class="btn-group">
            <?= Html::a(FA::icon(FA::_TRASH_O), [
                'delete-inbox-message',
                'inbox_message_id' => $inbox_message_model->id,
            ], [
                'class' => 'btn btn-danger btn-sm',
                'data-method' => 'post',
                'data-confirm' => Yii::t('notification', 'Are you sure you want to delete this message?')
            ]) ?>
        </div>

    </div>
    <div class="mailbox-read-message">
        <?= $inbox_message_model->message->text ?>
    </div>
</div>
<?php
$this->endContent();
?>
