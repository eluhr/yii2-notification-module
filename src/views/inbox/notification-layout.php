<?php
/**
 * --- VARIABLES ---
 *
 * @var string $content
 */

?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-sm-9">
        <div class="box box-primary">
            <?= $content ?>
        </div>
    </div>
</div>
