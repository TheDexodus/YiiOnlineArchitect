<?php

use auth\models\forms\LoginForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var LoginForm $loginForm */

$this->title = 'Login on site';

$form = ActiveForm::begin(
    [
        'id'          => 'login-form',
        'layout'      => 'horizontal',
        'fieldConfig' => [
            'template'     => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]
);

?>

<h1>Login</h1>

<?=$form->field($loginForm, 'email')->textInput()?>
<?=$form->field($loginForm, 'password')->passwordInput()?>

<div class="form-group">

    <div class="col-lg-offset-1 col-lg-11">
        <?=Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button'])?>
        <?=Html::a('Forgot password?', ['/password/restore'])?>
    </div>
</div>

<?php ActiveForm::end() ?>
