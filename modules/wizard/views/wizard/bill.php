<?php

use wizard\models\forms\WizardForm;
use yii\bootstrap\Html;

/** @var WizardForm $form */

$this->title = 'Wizard - Step 5';

$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<h1><?=Html::encode($this->title)?></h1>

<div class="bill">
    <table class="table">
        <thead>
        <tr>
            <th>Material Name</th>
            <th>Count</th>
            <th>Price per 1 unit</th>
            <th>Price per m<span style="vertical-align: super; font-size: 8pt">2</span></th>
            <th>Price</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($form->usage_material as $item): ?>
            <tr>
                <td><?=$form->goodMaterials[$item]->display_name?></td>
                <td><?=$form->getCount($item)?> <?=$form->goodMaterials[$item]->type->measurements?></td>
                <td><?=$form->goodMaterials[$item]->price?></td>
                <td><?=$form->goodMaterials[$item]->price * $form->goodMaterials[$item]->multiplier?></td>
                <td><?=$form->getPrice($item)?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th>Total Price</th>
            <th></th>
            <th></th>
            <th></th>
            <th><?=$form->getPrice()?></th>
        </tr>
        </tfoot>
    </table>
</div>