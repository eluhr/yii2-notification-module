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
        <?php
        $form = ActiveForm::begin(['method' => 'get', 'action' => [''],'id' => 'inbox-sort-form']);
        ?>
        <div class="btn-group btn-group-sm pull-left">
            <div class="btn">
                <?=$form->field($inbox_message_search_model,'sort')->checkbox(['class' => 'hidden'], false)->label(FA::icon(FA::_FILTER))?>
            </div>
        </div>
        <div class="input-group input-group-sm pull-right">
            <div class="has-feedback">
                <?= $form->field($inbox_message_search_model, 'q',['options' => ['class' => '']])->input('text', [
                    'placeholder' => Yii::t('notification', 'Search messages'),
                    'class' => 'form-control input-sm'
                ])->label(false)->error(false) ?>
                <?= FA::icon(FA::_SEARCH, ['class' => 'form-control-feedback']) ?>
            </div>
        </div>
        <?php
        ActiveForm::end();
        ?>
        <span class="clearfix"></span>
    </div>
    <span class="clearfix"></span>
</div>
