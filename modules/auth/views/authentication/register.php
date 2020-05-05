<?php

use auth\models\forms\RegisterForm;
use auth\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var RegisterForm $registerForm */

$this->title = 'Register on site';

$form = ActiveForm::begin(
    [
        'id'          => 'register-form',
        'layout'      => 'horizontal',
        'fieldConfig' => [
            'template'     => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]
);

?>

<h1>Register</h1>

<?=$form->field($registerForm, 'firstName')->textInput()?>
<?=$form->field($registerForm, 'lastName')->textInput()?>
<?=$form->field($registerForm, 'gender')->dropDownList([User::GENDER_MALE => 'Male', User::GENDER_FEMALE => 'Female'], ['prompt' => 'Select your gender'])?>
<?=$form->field($registerForm, 'birthday')->input('date')?>
<?=$form->field($registerForm, 'email')->textInput()?>
<?=$form->field($registerForm, 'password')->passwordInput()?>

<div class="form-group">

    <div class="col-lg-offset-1 col-lg-11">
        <?=Html::submitButton('Create account', ['class' => 'btn btn-primary', 'name' => 'register-button'])?>
    </div>
</div>

<?php ActiveForm::end() ?>
