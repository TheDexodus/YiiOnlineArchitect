<?php

use wizard\models\forms\WizardForm;
use yii\bootstrap\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */
/** @var WizardForm $form */

$this->title = 'Wizard - Step 2';

$this->params['breadcrumbs'][] = ['label' => $this->title];

$this->registerJs(
    <<<'JS'
$("#btn-new").click(function() {
  console.log('Hello :)')
  
  let newElement = '<div class="opening" id="opening-' + countOpenings + '">' +
      '                <button type="button" class="btn btn-danger" onclick="$(\'#opening-' + countOpenings + '\').remove()">-</button>'+
'                <label>'+
'                    Width'+
'                    <input type="number" name="WizardForm[openings][' + countOpenings + '][width]" value="0" min="0.01" step="0.01">'+
'                </label>'+
'                <label>'+
'                    Height'+
'                    <input type="number" name="WizardForm[openings][' + countOpenings + '][height]" value="0" min="0.01" step="0.01">'+
'                </label>'+
'            </div>';
  
  $(".openings").append(newElement)
  countOpenings++
})

JS
    ,
    View::POS_END
);

?>

<div class="wizard">
    <h1><?=Html::encode($this->title)?></h1>

    <div class="wizard-form">
        <?php $htmlForm = ActiveForm::begin(['id' => 'model', 'action' => '/wizard?step=3']); ?>

        <?=$htmlForm->field($form, 'floor_width')->hiddenInput()->label(false)?>
        <?=$htmlForm->field($form, 'floor_height')->hiddenInput()->label(false)?>
        <?=$htmlForm->field($form, 'wall_height')->hiddenInput()->label(false)?>

        <?=Html::button('Add new door or window', ['class' => 'btn btn-primary', 'id' => 'btn-new'])?>

        <?php $maxIdx = 0?>
        <?php foreach ($form->openings as $idx => $opening): ?>
            <?php $maxIdx = max($maxIdx, $idx)?>
            <div class="opening" id="opening-<?=$idx?>' + countOpenings + '">
                <button type="button" class="btn btn-danger" onclick="$('#opening-<?=$idx?>').remove()">-</button>
                <label>
                    Width
                    <input type="number" name="WizardForm[openings][<?=$idx?>][width]" value="<?=$opening['width']?>"
                           min="0.01"
                           step="0.01">
                </label>
                <label>
                    Height
                    <input type="number" name="WizardForm[openings][<?=$idx?>][height]" value="<?=$opening['height']?>"
                           min="0.01"
                           step="0.01">
                </label>
            </div>
        <?php endforeach; ?>
        <?php $this->registerJsVar('countOpenings', $maxIdx+1)?>

        <div class="openings">
        </div>

        <div class="form-group">
            <?=Html::submitButton('Next', ['class' => 'btn btn-success'])?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>