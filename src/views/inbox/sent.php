<?php
/**
 * --- VARIABLES ---
 *
 * @var ActiveDataProvider $sendInboxMessageDataProvider
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
    ['inboxMessageSearchModel' => $inboxMessageSearchModel, 'title' => Yii::t('notification', 'Sent')]) ?>
<div class="box-body">
    <?= GridView::widget(Inbox::sentGridViewConfig($sendInboxMessageDataProvider, $inboxMessageSearchModel, true)) ?>
</div>

<?php
$this->endContent();
?>
