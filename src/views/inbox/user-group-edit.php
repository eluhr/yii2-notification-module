<?php

use eluhr\notification\components\helpers\User as UserHelper;
use eluhr\notification\models\MessageUserGroup;
use kartik\select2\Select2;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * --- VARIABLES ---
 *
 * @var MessageUserGroup $message_user_group_model
 * @var View $this
 */
$this->beginContent(__DIR__ . '/notification-layout.php');
?>

<?php if (!$message_user_group_model->isNewRecord): ?>
    <div class="box-header">
        <div class="box-title pull-left"><?= $message_user_group_model->name ?></div>
        <div class="btn-group pull-right">
            <?= Html::a(FA::icon(FA::_TRASH_O), [
                'delete-user-group',
                'message_user_group_id' => $message_user_group_model->id,
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

        echo $form->field($message_user_group_model, 'owner_id')->hiddenInput()->label(false);

        echo $form->field($message_user_group_model, 'name')->textInput([
            'placeholder' => Yii::t('notification', 'Name:')
        ])->label(false);


        echo $form->field($message_user_group_model, 'receiver_ids')->widget(Select2::class, [
            'theme' => Select2::THEME_BOOTSTRAP,
            'data' => UserHelper::possibleUsers(),
            'options' => [
                'placeholder' => Yii::t('notification', 'Receivers:'),
                'multiple' => true
            ]
        ])->label(false);

        echo Html::errorSummary($message_user_group_model);

        echo Html::submitButton(Yii::t('notification', 'Save'), ['class' => 'btn btn-primary']);

        ActiveForm::end();
        ?>
    </div>
<?php
$this->endContent();
?>