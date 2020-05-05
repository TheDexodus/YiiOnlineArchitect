<?php

use crud\components\columns\HasManyColumn;
use crud\components\columns\HasOneColumn;
use crud\components\Model;
use crud\models\forms\ModelForm;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var Model $model */
/** @var ModelForm $form */
/** @var string $method */

$fields = [];

$htmlForm = ActiveForm::begin(['id' => 'model']);

foreach ($form->getModelColumns() as $columnName => $column) {
    if ($column instanceof HasOneColumn && $column->getMethod() === HasOneColumn::METHOD_SELECT) {
        /** @var ActiveRecord[] $records */
        $records = $column->getTargetClass()::find()->all();
        $items = [];

        $display_column = $column->getColumnDisplay();
        foreach ($records as $record) {
            if ($display_column === null) {
                $items[$record->getPrimaryKey()] = $record->getPrimaryKey();
            } else {
                $items[$record->getPrimaryKey()] = $record->$display_column;
            }
        }

        $fields[] = $htmlForm->field($form, $columnName)->dropDownList($items);
        continue;
    }
    if ($column instanceof HasManyColumn) {
        /** @var ActiveRecord[] $records */
        $records = $column->getTargetClass()::find()->all();
        $items = [];

        foreach ($records as $record) {
            $items[$record->getPrimaryKey()] = $record->getPrimaryKey();
        }

        $fields[] = $htmlForm->field($form, $columnName)->listBox($items, ['multiple' => true]);
        continue;
    }

    $action = $column->getFieldAction();
    $options = $column->getFieldOptions();
    $widget = $column->getWidget();

    if ($widget === null) {
        $fields[] = call_user_func_array([$htmlForm->field($form, $columnName), $action], $options);
    } else {
        $fields[] = $htmlForm->field($form, $columnName)->widget($widget, $options);
    }
}
?>

<div class="model-form">
    <?php foreach ($fields as $field): ?>
        <?=$field?>
    <?php endforeach; ?>

    <div class="form-group">
        <?=Html::submitButton($method, ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end(); ?>
</div>