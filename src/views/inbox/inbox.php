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
use eluhr\notification\models\Message;
use eluhr\notification\models\search\InboxMessage as InboxMessageSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;


$this->beginContent(__DIR__ . '/notification-layout.php');
?>


<?= $this->render('_header',
    ['inboxMessageSearchModel' => $inboxMessageSearchModel, 'title' => Yii::t('notification', 'Inbox')]) ?>

<div class="box-body">
    <?php
    $notEmptyOnQuery = empty($inboxMessageSearchModel->q) || (!empty($inboxMessageSearchModel->q) && (int)$unreadInboxMessageDataProvider->query->count());
    if ($notEmptyOnQuery): ?>
        <h5 class="box-title"><?= Yii::t('notification', 'Unread') ?></h5>
        <?= Html::beginForm(Url::to(['context-action']), 'POST'); ?>
        <div class="btn-group pull-right notification-context-btn-group">
            <?= Html::submitButton(\Yii::t('frontend', 'Mark selected as read'),
                [
                    'id' => 'mark-read-message',
                    'class' => 'btn btn-primary',
                    'name' => Message::SUBMIT_TYPE_NAME,
                    'value' => Message::MARK_MESSAGE_AS_READ
                ])
            ?>
            <?= Html::submitButton(\Yii::t('frontend', 'Delete selected'),
                [
                    'id' => 'delete-message',
                    'class' => 'btn btn-danger',
                    // TODO add the confirm and keep the name/value in the request
                    //'data-confirm' => "Are you sure you want to delete the selected messages?",
                    'name' => Message::SUBMIT_TYPE_NAME,
                    'value' => Message::DELETE_MESSAGE
                ]) ?>
        </div>
        <?= GridView::widget(Inbox::inboxGridViewConfig($unreadInboxMessageDataProvider,
            $inboxMessageSearchModel, $this->context->module->checkboxEnabled)) ?>
        <?= Html::endForm() ?>
    <?php endif; ?>
    <?php
    $emptyOnQuery = (int)$everythingElseInboxMessageDataProvider->query->count() !== 0;
    if ($emptyOnQuery):
        ?>
        <h5 class="box-title"><?= Yii::t('notification', 'Everything else') ?></h5>
        <?= Html::beginForm(Url::to(['context-action']), 'POST'); ?>
        <div class="btn-group pull-right notification-context-btn-group">
            <?= Html::submitButton(\Yii::t('frontend', 'Mark selected as read'),
                [
                    'id' => 'mark-read-message',
                    'class' => 'btn btn-primary',
                    'name' => Message::SUBMIT_TYPE_NAME,
                    'value' => Message::MARK_MESSAGE_AS_READ
                ])
            ?>
            <?= Html::submitButton(\Yii::t('frontend', 'Delete selected'),
                [
                    'id' => 'delete-message',
                    'class' => 'btn btn-danger',
                    // TODO add the confirm and keep the name/value in the request
                    //'data-confirm' => "Are you sure you want to delete the selected messages?",
                    'name' => Message::SUBMIT_TYPE_NAME,
                    'value' => Message::DELETE_MESSAGE
                ]) ?>
        </div>
        <?= GridView::widget(Inbox::inboxGridViewConfig($everythingElseInboxMessageDataProvider,
        $inboxMessageSearchModel, $this->context->module->checkboxEnabled)) ?>
        <?= Html::endForm() ?>
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
