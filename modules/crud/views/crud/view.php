<?php

use crud\components\columns\ColumnInterface;
use crud\components\columns\HasManyColumn;
use crud\components\columns\HasOneColumn;
use crud\components\columns\RelationColumnInterface;
use crud\components\helpers\NameHelper;
use crud\components\Model;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\grid\GridView;
use yii\widgets\DetailView;

/** @var Model $model */
/** @var ActiveRecord $fillModel */

$this->title = sprintf('View %s', $model->title);
if (Yii::$app->user->can('ROLE_VIEW_ALL_MODELS')) {
    $this->params['breadcrumbs'][] = ['label' => 'CRUD', 'url' => ['/admin/crud/']];
} else {
    $this->params['breadcrumbs'][] = ['label' => 'CRUD'];
}
if (Yii::$app->user->can('ROLE_'.strtoupper($model->name).'_VIEW')) {
    $this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['/admin/crud/'.$model->name.'/index']];
} else {
    $this->params['breadcrumbs'][] = ['label' => $model->title];
}
$this->params['breadcrumbs'][] = $fillModel->getPrimaryKey();

/**
 * @param ColumnInterface[] $columns
 * @param ActiveRecord      $fillModel
 *
 * @return array
 *
 * @throws Exception
 */
function getColumnsDetail(array $columns, ActiveRecord $fillModel): array
{
    $result = [];
    $lastIsRelation = false;

    foreach ($columns as $columnName => $column) {
        if ($column instanceof HasOneColumn) {
            $relationColumn = NameHelper::getRelationColumnName($columnName);
            $newModel = $fillModel->$relationColumn;

            if ($newModel instanceof ActiveRecord) {
                if (!$lastIsRelation) {
                    $result[] = ['label' => '', 'value' => ''];
                }
                if ($column->getMethod() === HasOneColumn::METHOD_CREATE) {
                    $result[] = ['label' => NameHelper::getColumnTitle($columnName), 'value' => ''];
                    $result = array_merge(
                        $result,
                        getColumnsDetail($column->getColumns(), $newModel)
                    );
                } elseif ($column->getMethod() === HasOneColumn::METHOD_SELECT) {
                    $value = 'id';
                    if ($column->getColumnDisplay() !== null) {
                        $value = $column->getColumnDisplay();
                    }

                    $result[] = ['label' => NameHelper::getColumnTitle($columnName), 'value' => $newModel->$value];
                }
                $result[] = ['label' => '', 'value' => ''];

                $lastIsRelation = true;
            } else {
                $result[] = ['label' => NameHelper::getColumnTitle($columnName), 'value' => 'Empty'];

                $lastIsRelation = false;
            }
        } else {
            $result[] = [
                'label' => NameHelper::getColumnTitle($columnName),
                'value' => $fillModel->getAttribute($columnName),
            ];
        }
    }

    return $result;
}

/**
 * @param ColumnInterface[] $columns
 * @param ActiveRecord      $fillModel
 *
 * @return array
 */
function getManyDetails(array $columns, ActiveRecord $fillModel): array
{
    $_relationData = [];
    foreach ($columns as $columnName => $column) {
        if ($column instanceof HasManyColumn) {
            $relationColumn = NameHelper::getRelationColumnName($columnName);
            $method = 'get'.ucfirst($relationColumn);
            $dataProvider = new ActiveDataProvider(
                [
                    'query'      => $fillModel->$method(),
                    'pagination' => [
                        'pageSize' => 5,
                    ],
                ]
            );

            $gridColumns = [];
            foreach ($column->getColumns() as $relationColumnName => $relationColumn) {
                if ($relationColumn instanceof RelationColumnInterface) {
                    continue;
                }

                $gridColumns[] = $relationColumnName;
            }

            $_relationData[$columnName] = [];
            $_relationData[$columnName]['dataProvider'] = $dataProvider;
            $_relationData[$columnName]['columns'] = $gridColumns;
        }
    }

    return $_relationData;
}

$details = getColumnsDetail($model->columns, $fillModel);
$_relationData = getManyDetails($model->columns, $fillModel);

?>

<div class="model-view">
    <h1><?=Html::encode($this->title)?></h1>
    <p>
        <?php if (Yii::$app->user->can('ROLE_'.strtoupper($model->name).'_UPDATE')): ?>
            <?=Html::a(
                'Update',
                ['/admin/crud/'.$model->name.'/update', 'id' => $fillModel->id],
                ['class' => 'btn btn-primary']
            )?>
        <?php endif; ?>
        <?php if (Yii::$app->user->can('ROLE_'.strtoupper($model->name).'_DELETE')): ?>
            <?=Html::a(
                'Delete',
                ['/admin/crud/'.$model->name.'/delete', 'id' => $fillModel->id],
                [
                    'class' => 'btn btn-danger',
                    'data'  => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method'  => 'post',
                    ],
                ]
            )?>
        <?php endif; ?>
    </p>

    <?=DetailView::widget(
        [
            'model'      => $model,
            'attributes' => $details,
            'template'   => '<tr><th{captionOptions}>{label}</th><td{contentOptions}>{value}</td></tr>',
        ]
    )?>

    <?php foreach ($_relationData as $relationName => $relationDatum): ?>
        <h2><?=NameHelper::getColumnTitle($relationName)?></h2>
        <?=GridView::widget(
            ['dataProvider' => $relationDatum['dataProvider'], 'columns' => $relationDatum['columns']]
        );?>
    <?php endforeach; ?>
</div>