<?php

use eluhr\notification\components\helpers\Inbox;
use eluhr\notification\models\search\InboxMessage as InboxMessageSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;

/**
 * --- VARIABLES ---
 *
 * @var ActiveDataProvider $send_inbox_message_data_provider
 * @var InboxMessageSearch $inbox_message_search_model
 * @var View $this
 */
$this->beginContent(__DIR__ . '/notification-layout.php');
?>


<?= $this->render('_header',
    ['inbox_message_search_model' => $inbox_message_search_model, 'title' => Yii::t('notification', 'Sent')]) ?>
<div class="box-body">
    <?= GridView::widget(Inbox::sentGridViewConfig($send_inbox_message_data_provider, $inbox_message_search_model)) ?>
</div>

<?php
$this->endContent();
?>
