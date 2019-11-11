<?php
/**
 * --- VARIABLES ---
 *
 * @var ActiveDataProvider $unreadInboxMessageDataProvider
 * @var ActiveDataProvider $everythingElseInboxMessageDataProvider
 * @var InboxMessageSearch $inboxMessageSearchModel
 * @var View $this
 */

use eluhr\notification\components\helpers\Inbox;
use eluhr\notification\models\search\InboxMessage as InboxMessageSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;

$this->beginContent(__DIR__ . '/notification-layout.php');
?>


<?= $this->render('_header',
    ['inboxMessageSearchModel' => $inboxMessageSearchModel, 'title' => Yii::t('notification', 'Inbox')]) ?>

<div class="box-body">
    <?php
    $notEmptyOnQuery = empty($inboxMessageSearchModel->q) || (!empty($inboxMessageSearchModel->q) && (int)$unreadInboxMessageDataProvider->query->count());
    if ($notEmptyOnQuery): ?>
        <h5 class="box-title"><?= Yii::t('notification', 'Unread') ?></h5>
        <?= GridView::widget(Inbox::inboxGridViewConfig($unreadInboxMessageDataProvider,
            $inboxMessageSearchModel)) ?>
    <?php endif; ?>
    <?php
    $emptyOnQuery = (int)$everythingElseInboxMessageDataProvider->query->count() !== 0;
    if ($emptyOnQuery):
        ?>
        <h5 class="box-title"><?= Yii::t('notification', 'Everything else') ?></h5>
        <?= GridView::widget(Inbox::inboxGridViewConfig($everythingElseInboxMessageDataProvider,
        $inboxMessageSearchModel)) ?>
    <?php
    endif;
    if (!$notEmptyOnQuery && !$emptyOnQuery):
        ?>
        <h4 class="text-muted"><?= Yii::t('notification', 'No messages found.') ?></h4>
    <?php endif; ?>
</div>

<?php
$this->endContent();
?>
