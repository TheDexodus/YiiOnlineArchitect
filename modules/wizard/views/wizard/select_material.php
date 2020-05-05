<?php

use app\models\Material;
use app\models\MaterialType;
use wizard\models\forms\WizardForm;
use yii\bootstrap\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var array $materials */
/** @var WizardForm $form */
/** @var View $this */

$this->title = 'Wizard - Step 3';

$this->params['breadcrumbs'][] = ['label' => $this->title];

$this->registerJsVar('selectedElements', $form->materials);
$this->registerJsVar('usages', $form->usage_material);
$this->registerJs(
    <<<'JS'

console.log(selectedElements)

let displayBlocks = function() {
  let types = ['floors', 'walls', 'cells']
  types.forEach(function(type) {
      if (usages.indexOf(type) === -1) {
        $("#block-" + type).hide(1000);
      } else {
        $("#block-" + type).show(1000);
      }
  })
}

$('#usage-list label input').change(function () {
  if (this.checked) {
    if (usages.indexOf(this.value) === -1) {
      usages.push(this.value)
    }
  } else {
    let idx = usages.indexOf(this.value)
    if (idx !== -1) {
      usages.splice(idx, 1)
    }
  }
  
  displayBlocks()
})

$('.block').click(function () {
  let id = this.id.replace('material-', '')
  
  let parent = $(this).parent().get(0).id.replace('block-', '');
  
  if (parent in selectedElements && selectedElements[parent] === id) {
    $('#material-' + selectedElements[parent]).removeClass('block-active')
    $('#material-' + selectedElements[parent]).find('.label-use').hide()
    $('.input-' + parent).remove();
    delete selectedElements[parent]
  } else {
    if (parent in selectedElements) {
      $('#material-' + selectedElements[parent]).removeClass('block-active')
      $('#material-' + selectedElements[parent]).find('.label-use').hide()
      $('.input-' + parent).remove()
    }
    selectedElements[parent] = id
    $('#material-' + selectedElements[parent]).addClass('block-active')
    $('#material-' + selectedElements[parent]).find('.label-use').show()
    $('#material-' + selectedElements[parent]).append('<input class="input-' + parent + '" type="hidden" name="WizardForm[materials][' + parent + ']" value="' + id + '">')
  }
})

let timerId = null

$('.block').mouseenter(function () {
  timerId = setTimeout(function(el) {
    $(el).popover('show')
  }, 1000, this)
})

$('.block').mouseleave(function () {
  if (timerId !== null) {
    $(this).popover('hide')
    clearTimeout(timerId)
    timerId = null
  }
})

displayBlocks()

JS
    ,
    View::POS_END
);

$this->registerCss(
    <<<'CSS'

.block {
    transition: 0.25s;
}

.block:hover {
    background-color: #acacac;
    box-shadow: #acacac 0 0 4px 4px;
    transition: 0.5s;
}

.block-active, .block-active:hover {
    background-color: #acc8ac;
    box-shadow: #acc8ac 0 0 4px 4px;
}

CSS
);

?>
<div class="wizard">
    <h1><?=Html::encode($this->title)?></h1>

    <div class="wizard-form">
        <?php $htmlForm = ActiveForm::begin(['id' => 'model', 'action' => '/wizard?step=4']); ?>

        <?=$htmlForm->field($form, 'floor_width')->hiddenInput()->label(false)?>
        <?=$htmlForm->field($form, 'floor_height')->hiddenInput()->label(false)?>
        <?=$htmlForm->field($form, 'wall_height')->hiddenInput()->label(false)?>
        <div class="form-group openings">
            <?php foreach ($form->openings as $idx => $opening): ?>
                <input type="hidden" class="form-control" name="WizardForm[openings][<?=$idx?>][width]"
                       value="<?=$opening['width']?>">
                <input type="hidden" class="form-control" name="WizardForm[openings][<?=$idx?>][height]"
                       value="<?=$opening['height']?>">
            <?php endforeach ?>
        </div>
        <?=$htmlForm->field($form, 'usage_material')->checkboxList(
            [
                MaterialType::USAGE_FLOORS => 'Floors',
                MaterialType::USAGE_WALLS  => 'Walls',
                MaterialType::USAGE_CELLS  => 'Cells',
            ],
            ['class' => 'checkbox', 'id' => 'usage-list']
        )?>

        <?php foreach ($materials as $key => $typeMaterials): ?>
            <div id="block-<?=$key?>"<?php if (!in_array(
                $key,
                $form->usage_material
            )): ?> style="display: none"<?php endif ?>>
                <h1><?=$key?></h1>
                <?php /** @var Material $material */ ?>
                <?php foreach ($typeMaterials as $material): ?>
                    <?php $details = json_decode($material->details) ?>
                    <div style="display: flex; margin: 8px 0; justify-content: space-between"
                         class="block<?php if ($material->vendor_code == (isset($form->materials[$key])
                                 ? $form->materials[$key] : null)): ?> block-active<?php endif ?>"
                         id="material-<?=$material->vendor_code?>"
                         data-toggle="popover" data-placement="top"
                         data-content="<?=isset($details->text) ? $details->text : 'Material for '.$key?>">
                        <div style="display: flex;">
                            <?php if ($material->use_pattern === 'picture'): ?>
                                <img style="width: 108px; height: 108px; margin: 0 8px 0 0"
                                     src="<?='/img/materials/'.$material->picture?>" alt="">
                            <?php else: ?>
                                <div style="background-color: <?=$material->color?>; width: 108px; height: 108px; margin: 0 8px 0 0"></div>
                            <?php endif ?>
                            <div style="display: flex; flex-direction: column;">
                                <h4><?=$material->display_name?></h4>
                                <h5>Price: <?=$material->price?> per <?=$material->type->measurements?></h5>
                                <h5>Price per m^2: <?=$material->price * $material->multiplier?></h5>
                            </div>
                        </div>
                        <label style="margin: auto 0;<?php if ($material->vendor_code != (isset($form->materials[$key])
                                ? $form->materials[$key] : null)): ?> display: none;<?php endif ?>" class="label-use">
                            Use this material
                        </label>
                        <?php if ($material->vendor_code == (isset($form->materials[$key])? $form->materials[$key] : null)): ?>
                            <input class="input-<?=$key?>" type="hidden" name="WizardForm[materials][<?=$key?>]" value="<?=$form->materials[$key]?>">
                        <?php endif ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <script>
          setTimeout(2000, function () {
            console.log('loaded')
            $('#usage-list label input').change(function () {
              console.log('Select')
            })
          })
        </script>

        <div class="form-group">
            <?=Html::submitButton('Next', ['class' => 'btn btn-success'])?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>