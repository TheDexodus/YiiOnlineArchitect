<?php

use app\models\Material;
use yii\bootstrap\Html;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

/** @var ArrayDataProvider $materialProvider */
/** @var ArrayDataProvider $materialTypeProvider */

$this->title = 'Import has been successful';

?>

<div class="category-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?=Html::a('Import new materials', ['/admin/importer'], ['class' => 'btn btn-success'])?>

    <?=GridView::widget(
        [
            'dataProvider' => $materialTypeProvider,
            'columns'      => [
                'id',
                'display_name',
                'usage',
                'measurements',
                'typical_consumption',
            ],
        ]
    );?>

    <?=GridView::widget(
        [
            'dataProvider' => $materialProvider,
            'columns'      => [
                'id',
                'vendor_code',
                'display_name',
                [
                    'label' => 'Type',
                    'value' => function (Material $material) {
                        return $material->type->display_name;
                    },
                ],
                'color',
                'picture',
                'price',
                'multiplier',
                //'details'
            ],
        ]
    );?>
</div>
