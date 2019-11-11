<?php
/**
 * --- VARIABLES ---
 *
 * @var MessageUserGroup $messageUserGroupModel
 * @var View $this
 */

use eluhr\notification\components\helpers\User as UserHelper;
use eluhr\notification\models\MessageUserGroup;
use kartik\select2\Select2;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


$this->beginContent(__DIR__ . '/notification-layout.php');
?>

<?php if (!$messageUserGroupModel->isNewRecord): ?>
    <div class="box-header">
        <div class="box-title pull-left"><?= $messageUserGroupModel->name ?></div>
        <div class="btn-group pull-right">
            <?= Html::a(FA::icon(FA::_TRASH_O), [
                'delete-user-group',
                'messageUserGroupId' => $messageUserGroupModel->id,
            ], [
                'class' => 'btn btn-danger btn-sm',
                'data-method' => 'post',
                'data-confirm' => Yii::t('notification', 'Are you sure you want to delete this user group?')
            ]) ?>
        </div>
    </div>
<?php endif; ?>

    <div class="box-body">
        <?php

        $form = ActiveForm::begin();

        echo $form->field($messageUserGroupModel, 'owner_id')->hiddenInput()->label(false);

        echo $form->field($messageUserGroupModel, 'name')->textInput([
            'placeholder' => Yii::t('notification', 'Name:')
        ])->label(false);


        echo $form->field($messageUserGroupModel, 'receiverIds')->widget(Select2::class, [
            'theme' => Select2::THEME_BOOTSTRAP,
            'data' => UserHelper::possibleUsers(),
            'options' => [
                'placeholder' => Yii::t('notification', 'Receivers:'),
                'multiple' => true
            ]
        ])->label(false);

        echo Html::errorSummary($messageUserGroupModel);

        echo Html::submitButton(Yii::t('notification', 'Save'), ['class' => 'btn btn-primary']);

        ActiveForm::end();
        ?>
    </div>
<?php
$this->endContent();
?>
