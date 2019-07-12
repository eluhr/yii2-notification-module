<?php

use dosamigos\ckeditor\CKEditor;
use eluhr\notification\models\Message;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * --- VARIABLES ---
 *
 * @var Message $message_model
 * @var View $this
 */
$this->beginContent(__DIR__ . '/notification-layout.php');
?>

    <div class="box-body">
        <?php

        $form = ActiveForm::begin();

        echo $form->field($message_model, 'receiver_ids')->widget(Select2::class, [
            'theme' => Select2::THEME_BOOTSTRAP,
            'data' => $message_model::possibleRecipients(),
            'options' => [
                'placeholder' => Yii::t('notification', 'To:'),
                'multiple' => true
            ]
        ])->label(false);

        echo $form->field($message_model, 'author_id')->hiddenInput()->label(false);

        echo $form->field($message_model, 'subject')->textInput([
            'placeholder' => Yii::t('notification', 'Subject:')
        ])->label(false);

        echo $form->field($message_model, 'text')->widget(CKEditor::class, [
            'preset' => 'custom',
            'clientOptions' => [
                'toolbar' => [
                    ['Undo', 'Redo'],
                    ['Bold', 'Italic', 'Underline', 'RemoveFormat','Link'],
                    ['Blockquote']
                ],
                'resize_enabled' => false,
                'removePlugins' => 'elementspath'
            ]
        ])->label(false);

        echo Html::errorSummary($message_model);

        echo Html::submitButton(Yii::t('notification', 'Send'), ['class' => 'btn btn-primary']);

        ActiveForm::end();
        ?>
    </div>
<?php
$this->endContent();
?>