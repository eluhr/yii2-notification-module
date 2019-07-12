<?php

use eluhr\notification\components\helpers\Inbox;
use eluhr\notification\models\search\InboxMessage as InboxMessageSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;

/**
 * --- VARIABLES ---
 *
 * @var ActiveDataProvider $unread_inbox_message_data_provider
 * @var ActiveDataProvider $everything_else_inbox_message_data_provider
 * @var InboxMessageSearch $inbox_message_search_model
 * @var View $this
 */
$this->beginContent(__DIR__ . '/notification-layout.php');
?>


<?= $this->render('_header',
    ['inbox_message_search_model' => $inbox_message_search_model, 'title' => Yii::t('notification', 'Inbox')]) ?>

<div class="box-body">
    <?php
    $not_empty_on_query = empty($inbox_message_search_model->q) || (!empty($inbox_message_search_model->q) && (int)$unread_inbox_message_data_provider->query->count());
    if ($not_empty_on_query): ?>
        <h5 class="box-title"><?= Yii::t('notification', 'Unread') ?></h5>
        <?= GridView::widget(Inbox::inboxGridViewConfig($unread_inbox_message_data_provider,
            $inbox_message_search_model)) ?>
    <?php endif; ?>
    <?php
    $empty_on_query = (int)$everything_else_inbox_message_data_provider->query->count() !== 0;
    if ($empty_on_query):
        ?>
        <h5 class="box-title"><?= Yii::t('notification', 'Everything else') ?></h5>
        <?= GridView::widget(Inbox::inboxGridViewConfig($everything_else_inbox_message_data_provider,
        $inbox_message_search_model)) ?>
    <?php
    endif;
    if (!$not_empty_on_query && !$empty_on_query):
        ?>
        <h4 class="text-muted"><?= Yii::t('notification', 'No messages found.') ?></h4>
    <?php endif; ?>
</div>

<?php
$this->endContent();
?>
