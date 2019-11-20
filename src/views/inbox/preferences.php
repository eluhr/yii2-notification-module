<?php
/**
 * --- VARIABLES ---
 *
 * @var MessagePreferences $model
 * @var View $this
 *
 */

use eluhr\notification\models\MessagePreferences;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->beginContent(__DIR__ . '/notification-layout.php');
?>

<div class="box-header with-border">
    <h3 class="box-title"><?= Yii::t('notification', 'Preferences') ?></h3>
</div>

<div class="box-body">
    <?php
    $form = ActiveForm::begin()
    ?>

    <?= $form->field($model, 'wants_to_additionally_receive_messages_by_mail')->checkbox() ?>

    <?php
    echo Html::errorSummary($model);
    echo Html::submitButton(Yii::t('notification', 'Save'), ['class' => 'btn btn-primary']);
    ActiveForm::end();
    ?>
</div>

<?php
$this->endContent();
?>
