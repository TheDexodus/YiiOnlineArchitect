<?php

use auth\models\forms\ProfileForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var ProfileForm $profileForm */

$this->title = 'Change Password';

$form = ActiveForm::begin(
    [
        'id'          => 'change-form',
        'layout'      => 'horizontal',
        'fieldConfig' => [
            'template'     => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]
);

?>

    <h1>Change Password</h1>

<?=$form->field($profileForm, 'oldPassword')->passwordInput()?>
<?=$form->field($profileForm, 'newPassword')->passwordInput()?>
<?=$form->field($profileForm, 'confirmNewPassword')->passwordInput()?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?=Html::submitButton('Change', ['class' => 'btn btn-primary', 'name' => 'change-button'])?>
        </div>
    </div>

<?php ActiveForm::end() ?>