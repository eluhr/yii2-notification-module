<?php
/**
 * --- VARIABLES ---
 *
 * @var InboxMessage[] $inboxMessages
 * @var string $moduleId
 * @var int $inboxMessagesCount
 * @var bool $hasMessages
 */

use eluhr\notification\models\InboxMessage;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<li class="dropdown messages-menu">
    <?php
    echo Html::beginTag('a', [
        'href' => $hasMessages ? '#' : Url::to(['/' . $moduleId . '/inbox/index']),
        'class' => $hasMessages ? 'dropdown-toggle' : false,
        'data-toggle' => $hasMessages ? 'dropdown' : false
    ]);
    echo FA::icon(FA::_ENVELOPE_O);
    if ($hasMessages) {
        echo Html::tag('span', $inboxMessagesCount, ['class' => 'label label-danger']);
    }
    echo Html::endTag('a');
    ?>
    <?php
    if ($hasMessages):
        ?>
        <ul class="dropdown-menu">
            <li>
                <ul class="menu">
                    <?php
                    foreach ($inboxMessages as $inboxMessage):
                        $message = $inboxMessage->message;
                        ?>
                        <li>
                            <a href="<?= Url::to([
                                '/' . $moduleId . '/inbox/read',
                                'inboxMessageId' => $inboxMessage->id
                            ]) ?>">
                                <h4>
                                    <?= $message->author->username ?>
                                    <small><?= FA::icon(FA::_CLOCK_O) ?> <?= Yii::$app->formatter->asRelativeTime($message->send_at) ?></small>
                                </h4>
                                <p><?= Html::encode($message->subject) ?></p>
                            </a>
                        </li>
                    <?php
                    endforeach;
                    ?>
                </ul>
            </li>
            <li class="text-center">
                <?= Html::a(Yii::t('notification', 'See all messages'), ['/' . $moduleId . '/inbox/index']) ?>
            </li>
        </ul>
    <?php
    endif;
    ?>
</li>
