<?php

use auth\models\forms\RestoreForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var RestoreForm $restoreForm */

$this->title = 'Restore Password';

$form = ActiveForm::begin(
    [
        'id'          => 'restore-form',
        'layout'      => 'horizontal',
        'fieldConfig' => [
            'template'     => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]
);

?>

    <h1>Restore Password</h1>

<?=$form->field($restoreForm, 'email')->textInput()?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?=Html::submitButton('Restore', ['class' => 'btn btn-primary', 'name' => 'restore-button'])?>
        </div>
    </div>

<?php ActiveForm::end() ?>