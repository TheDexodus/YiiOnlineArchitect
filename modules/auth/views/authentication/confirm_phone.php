<?php

use auth\models\forms\RegisterForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/** @var RegisterForm $registerForm */
/** @var int $renewTime */

$this->title = 'Confirm Email';

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
<style>
    .disabled, .disabled:hover {
        color: currentColor;
        cursor: not-allowed;
        opacity: 0.5;
        text-decoration: none;
        pointer-events: none;
    }
</style>

<h1>Enter your phone</h1>

<?=$form->field($registerForm, 'confirmPhoneCode')->textInput()?>

<div class="form-group">
    <div class="col-lg-offset-1 col-lg-11">
        <?=Html::submitButton('Confirm Phone', ['class' => 'btn btn-primary', 'name' => 'set-phone-button'])?>
        <?=Html::a('Renew <span></span>', ['/phone/set'], ['class' => 'disabled', 'id' => 'renew'])?>
    </div>
</div>

<?php ActiveForm::end() ?>

<script>
  let renewTime = <?=$renewTime?>

  var checkRenewTime = function () {
    if (Math.floor($.now() / 1000) >= renewTime) {
      $('#renew').removeClass('disabled')
      $('#renew').find('span').text('')

      clearInterval(intervalId)
    } else {
      $('#renew').find('span').text(renewTime - Math.floor($.now() / 1000))
    }
  }

  const intervalId = setInterval(checkRenewTime, 1000)
</script>