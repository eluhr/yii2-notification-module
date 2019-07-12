<?php

use eluhr\notification\components\helpers\Permission;
use eluhr\notification\models\Message;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\web\View;

/**
 * --- VARIABLES ---
 *
 * @var Message $message_model
 * @var View $this
 */

$this->beginContent(__DIR__ . '/notification-layout.php');
?>

<div class="box-body no-padding">

    <div class="box-header">
        <div class="btn-group pull-right">
            <?php
            $previous_message_model = $message_model->previous;
            echo Html::a(FA::icon(FA::_CHEVRON_LEFT),
                $previous_message_model ? ['read-sent', 'message_id' => $previous_message_model->id] : '#',
                ['class' => 'btn btn-default' . ($previous_message_model === null ? ' disabled' : '')]) ?>
            <?php
            $next_message_model = $message_model->next;
            echo Html::a(FA::icon(FA::_CHEVRON_RIGHT),
                $next_message_model !== null ? ['read-sent', 'message_id' => $next_message_model->id] : '#',
                ['class' => 'btn btn-default' . ($next_message_model === null ? ' disabled' : '')]) ?>
        </div>
    </div>
    <div class="mailbox-read-info">
        <h3><?= $message_model->subject ?></h3>
        <h5><?= Yii::t('notification', 'From: {author-username}',
                ['author-username' => $message_model->author->username]) ?>
            <span class="mailbox-read-time pull-right"><?= Yii::$app->formatter->asRelativeTime($message_model->send_at) ?></span>
        </h5>
    </div>
    <div class="mailbox-controls with-border text-center">
        <div class="btn-group">
            <?php if (Yii::$app->user->can(Permission::COMPOSE_A_MESSAGE)): ?>
                <?= Html::a(FA::icon(FA::_REPLY), [
                    'compose',
                    'message_id' => $message_model->id,
                    'reply_to' => $message_model->author_id
                ], ['class' => 'btn btn-default btn-sm']) ?>

                <?= Html::a(FA::icon(FA::_SHARE), [
                    'compose',
                    'message_id' => $message_model->id,
                ], ['class' => 'btn btn-default btn-sm']) ?>
            <?php endif; ?>
        </div>

    </div>
    <div class="mailbox-read-message">
        <?= $message_model->text ?>
    </div>
</div>
<?php
$this->endContent();
?>
