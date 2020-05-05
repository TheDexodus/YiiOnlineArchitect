<?php

use auth\models\forms\ProfileForm;
use borales\extensions\phoneInput\PhoneInput;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var ProfileForm $profileForm */

$this->title = 'Enter your phone';

$form = ActiveForm::begin(
    [
        'id'          => 'profile-form',
        'layout'      => 'horizontal',
        'fieldConfig' => [
            'template'     => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]
);

?>

    <h1>Enter your phone</h1>

<?=$form->field($profileForm, 'phone')->widget(PhoneInput::class)?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?=Html::submitButton('Enter phone', ['class' => 'btn btn-primary', 'name' => 'set-phone-button'])?>
        </div>
    </div>

<?php ActiveForm::end() ?>