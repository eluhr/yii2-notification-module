<?php
/**
 * --- VARIABLES ---
 *
 * @var Message $messageModel
 * @var View $this
 */

use dosamigos\ckeditor\CKEditor;
use eluhr\notification\components\helpers\Permission;
use eluhr\notification\models\Message;
use kartik\select2\Select2;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->beginContent(__DIR__ . '/notification-layout.php');
?>

    <div class="box-body">
        <?php

        $form = ActiveForm::begin();

        echo $form->field($messageModel, 'receiverIds')->widget(Select2::class, [
            'theme' => Select2::THEME_BOOTSTRAP,
            'data' => $messageModel::possibleRecipients(),
            'options' => [
                'placeholder' => Yii::t('notification', 'To:'),
                'multiple' => true
            ]
        ])->label(false);

        echo $form->field($messageModel, 'author_id')->hiddenInput()->label(false);

        echo $form->field($messageModel, 'subject')->textInput([
            'placeholder' => Yii::t('notification', 'Subject:')
        ])->label(false);

        echo $form->field($messageModel, 'text')->widget(CKEditor::class, [
            'preset' => 'custom',
            'clientOptions' => [
                'toolbar' => [
                    ['Undo', 'Redo'],
                    ['Bold', 'Italic', 'Underline', 'RemoveFormat', 'Link'],
                    ['Blockquote']
                ],
                'resize_enabled' => false,
                'removePlugins' => 'elementspath'
            ]
        ])->label(false);

        if (Yii::$app->user->can(Permission::SEND_PRIORITY_MESSAGE)):
            ?>
            <div class="box box-default collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Yii::t('notification', 'Further options') ?></h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <?= FA::icon(FA::_PLUS) ?>
                        </button>
                    </div>
                </div>
                <div class="box-body" style="display: none;">
                    <?= $form->field($messageModel, 'priority')->widget(Select2::class, [
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'data' => $messageModel::priorities(),
                        'hideSearch' => true,
                        'pluginOptions' => ['allowClear' => true],
                        'options' => [
                            'placeholder' => Yii::t('notification', 'Priority:'),
                        ]
                    ])->label(false) ?>
                </div>
            </div>
        <?php
        endif;
        echo Html::errorSummary($messageModel);

        echo Html::submitButton(Yii::t('notification', 'Send'), ['class' => 'btn btn-primary']);

        ActiveForm::end();
        ?>
    </div>
<?php
$this->endContent();
?>
