<?php

use crud\components\columns\RelationColumnInterface;
use crud\components\Model;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;

/** @var Model $model */
/** @var ActiveDataProvider $dataProvider */

$this->title = sprintf('%s List', $model->title);
if (Yii::$app->user->can('ROLE_VIEW_ALL_MODELS')) {
    $this->params['breadcrumbs'][] = ['label' => 'CRUD', 'url' => ['/admin/crud/']];
} else {
    $this->params['breadcrumbs'][] = ['label' => 'CRUD'];
}
$this->params['breadcrumbs'][] = $model->title;

$columns = [];
$columns[] = ['class' => 'yii\grid\SerialColumn'];

foreach ($model->columns as $columnName => $column) {
    if ($column instanceof RelationColumnInterface) {
        continue;
    }
    $columns[] = $columnName;
}

$userCanActions = [];
if (Yii::$app->user->can('ROLE_'.strtoupper($model->name).'_UPDATE')) {
    $userCanActions[] = Model::ACTION_UPDATE;
}
if (Yii::$app->user->can('ROLE_'.strtoupper($model->name).'_VIEW')) {
    $userCanActions[] = Model::ACTION_VIEW;
}
if (Yii::$app->user->can('ROLE_'.strtoupper($model->name).'_DELETE')) {
    $userCanActions[] = Model::ACTION_DELETE;
}

foreach ($model->actions as $action) {
    unset($userCanActions[$action]);
}

if (count($userCanActions) > 0) {
    $columns[] = [
        'class' => 'yii\grid\ActionColumn',
        'urlCreator' => function ($action, $_model, $key, $index) use ($model) {
            $params = is_array($key) ? $key : ['id' => (string) $key];
            $params[0] = sprintf('/admin/crud/%s/%s', $model->name, $action);

            return Url::toRoute($params);
        },
        'template' => implode(
            ' ',
            array_map(
                function ($action) {
                    return sprintf('{%s}', $action);
                },
                $userCanActions
            )
        ),
    ];
}

?>

<div class="category-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php if ($model->canAction(Model::ACTION_CREATE) && Yii::$app->user->can(
            'ROLE_'.strtoupper($model->name).'_CREATE'
        )): ?>
        <p>
            <?=Html::a(
                sprintf('Create %s', $model->title),
                ['/admin/crud/'.$model->name.'/create'],
                ['class' => 'btn btn-success']
            )?>
        </p>
    <?php endif ?>

    <?=GridView::widget(['dataProvider' => $dataProvider, 'columns' => $columns]);?>
</div>