<?php

/**
 * --- VARIABLES ---
 *
 * @var InboxMessageSearch $inbox_message_search_model
 * @var string $title
 */

use eluhr\notification\models\search\InboxMessage as InboxMessageSearch;
use rmrevin\yii\fontawesome\FA;
use yii\widgets\ActiveForm;

?>
<div class="box-header with-border">
    <h3 class="box-title pull-left"><?= $title ?></h3>
    <div class="box-tools pull-right">
        <div class="input-group input-group-sm">
            <?php
            $form = ActiveForm::begin(['method' => 'get', 'action' => ['']]);
            ?>
            <div class="has-feedback">
                <?= $form->field($inbox_message_search_model, 'q',
                    ['options' => ['class' => 'hh']])->input('text', [
                    'placeholder' => Yii::t('notification', 'Search messages'),
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
