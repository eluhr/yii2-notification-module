<?php
/**
 * --- VARIABLES ---
 *
 * @var MessageUserGroup $messageUserGroupSearchModel
 * @var ActiveDataProvider $messageUserGroupDataProvider
 * @var View $this
 */

use eluhr\notification\components\helpers\Inbox;
use eluhr\notification\models\search\MessageUserGroup;
use rmrevin\yii\fontawesome\FA;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->beginContent(__DIR__ . '/notification-layout.php');
?>


<div class="box-header with-border">
    <h3 class="box-title pull-left"><?= Yii::t('notification', 'User Groups') ?></h3>
    <div class="box-tools pull-right">
        <div class="input-group input-group-sm">
            <?php
            $form = ActiveForm::begin(['method' => 'get', 'action' => ['']]);
            ?>
            <div class="has-feedback">
                <?= $form->field($messageUserGroupSearchModel, 'q',
                    ['options' => ['class' => 'hh']])->input('text', [
                    'placeholder' => Yii::t('notification', 'Search user groups'),
                    'class' => 'form-control input-sm'
                ])->label(false)->error(false) ?>
                <?= FA::icon(FA::_SEARCH, ['class' => 'form-control-feedback']) ?>
            </div>
            <?php
            ActiveForm::end();
            ?>
        </div>
    </div>
    <span class="clearfix"></span>
</div>


<div class="box-body">
    <?= GridView::widget(Inbox::userGroupGridViewConfig($messageUserGroupDataProvider,
        $messageUserGroupSearchModel)) ?>
    <?= Html::a(Yii::t('notification', 'Add new user group'), ['user-group-edit'],
        ['class' => 'btn btn-primary btn-block']) ?>
</div>

<?php
$this->endContent();
?>
