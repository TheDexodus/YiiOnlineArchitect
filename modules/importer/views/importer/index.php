<?php

use importer\models\forms\ImportForm;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/** @var ImportForm $form */

$this->title = 'Import materials';

$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="model-import">
    <h1><?=Html::encode($this->title)?></h1>

    <div class="import-form">
        <?php $htmlForm = ActiveForm::begin(['id' => 'model', 'options' => ['enctype' => 'multipart/form-data']]); ?>

        <?=$htmlForm->field($form, 'file')->fileInput()?>

        <div class="form-group">
            <?=Html::submitButton('Import', ['class' => 'btn btn-success'])?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>