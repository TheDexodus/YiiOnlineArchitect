<?php

use wizard\models\forms\WizardForm;
use yii\bootstrap\Html;

/** @var WizardForm $form */

$this->title = 'Wizard - Step 5';

$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<h1><?=Html::encode($this->title)?></h1>

<div class="bill">
    <form>
        <?php foreach ($form->usage_material as $item): ?>
            <div class="form-group">
                <label>
                    Material price for <?=$item?>
                    <input class="form-control" type="text" placeholder="<?=$form->getPrice($item)?>" readonly>
                </label>
            </div>
        <?php endforeach; ?>

        <div class="form-group">
            <label>
                All price
                <input class="form-control" type="text" placeholder="<?=$form->getPrice()?>" readonly>
            </label>
        </div>
    </form>
</div>