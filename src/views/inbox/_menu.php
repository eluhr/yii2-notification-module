<?php

use eluhr\notification\components\helpers\Permission;
use eluhr\notification\models\InboxMessage;
use eluhr\notification\models\Message;
use eluhr\notification\models\MessageUserGroup;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php if (Yii::$app->user->can(Permission::COMPOSE_A_MESSAGE)): ?>
    <?= Html::a(Yii::t('notification', 'Compose'), ['compose'],
        ['class' => 'btn btn-primary btn-block margin-bottom']) ?>
<?php endif; ?>
<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('notification', 'Folders') ?></h3>
    </div>
    <div class="box-body no-padding">
        <ul class="nav nav-pills nav-stacked">
            <li class="<?= in_array(Yii::$app->controller->action->id , ['index','read','compose']) ? 'active' : null ?>">
                <a href="<?= Url::to(['index']) ?>">
                    <?= FA::icon(FA::_INBOX) ?>
                    <?= Yii::t('notification', 'Inbox') ?>
                    <span class="label label-primary pull-right"><?= InboxMessage::find()->hideSoftDeleted()->own()->andWhere(['read' => 0])->count() ?></span>
                </a>
            </li>
            <?php if (Yii::$app->user->can(Permission::COMPOSE_A_MESSAGE)): ?>
            <li class="<?= Yii::$app->controller->action->id === 'sent' || Yii::$app->controller->action->id === 'read-sent' ? 'active' : null ?>">
                <a href="<?= Url::to(['sent']) ?>">
                    <?= FA::icon(FA::_ENVELOPE_O) ?>
                    <?= Yii::t('notification', 'Sent') ?>
                    <span class="label label-default pull-right"><?= Message::find()->andWhere(['author_id' => Yii::$app->user->id])->count() ?></span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (Yii::$app->user->can(Permission::USER_GROUPS)): ?>
                <li class="<?= Yii::$app->controller->action->id === 'user-group' || Yii::$app->controller->action->id === 'user-group-edit' ? 'active' : null ?>">
                    <a href="<?= Url::to(['user-group']) ?>">
                        <?= FA::icon(FA::_USERS) ?>
                        <?= Yii::t('notification', 'User Groups') ?>
                        <span class="label label-success pull-right"><?= MessageUserGroup::find()->own()->count() ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="<?= Yii::$app->controller->action->id === 'preferences' ? 'active' : null ?>">
                <a href="<?= Url::to(['preferences']) ?>">
                    <?= FA::icon(FA::_COG) ?>
                    <?= Yii::t('notification', 'Preferences') ?>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="form-group">
    <?= Html::a(Yii::t('notification', 'Reload'), Yii::$app->request->url,
        ['class' => 'btn btn-block btn-default margin-bottom']) ?>
</div>
