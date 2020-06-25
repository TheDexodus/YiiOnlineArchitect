<?php

use wizard\models\forms\WizardForm;
use yii\bootstrap\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */
/** @var WizardForm $form */

$this->title = 'Wizard - Step 1';

$this->params['breadcrumbs'][] = ['label' => $this->title];

$this->registerCss(
    <<<'CSS'

div.unit-meters label:after {
    content: " (meters)";
    color: gray;
}

CSS
);

?>

<div class="wizard">
    <h1><?=Html::encode($this->title)?></h1>

    <div class="wizard-form">
        <?php $htmlForm = ActiveForm::begin(['id' => 'model', 'action' => '/wizard?step=2']); ?>

        <div class="unit-meters">
            <?=$htmlForm->field($form, 'floor_width')->input('number', ['step' => 0.01])?>
            <?=$htmlForm->field($form, 'floor_height')->input('number', ['step' => 0.01])?>
            <?=$htmlForm->field($form, 'wall_height')->input('number', ['step' => 0.01])?>
        </div>

        <div class="form-group">
            <?=Html::submitButton('Next', ['class' => 'btn btn-success'])?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>