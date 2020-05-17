<?php

use app\models\Material;
use app\models\MaterialType;
use crud\components\columns\HasOneColumn;

return [
    'class'   => Material::class,
    'title'   => 'Material',
    'columns' => [
        'vendor_code'  => [
            'type'       => 'number',
            'validators' => [
                ['unique', 'targetClass' => Material::class, 'targetAttribute' => 'vendor_code'],
            ],
        ],
        'display_name' => [
            'type'       => 'text',
            'validators' => ['required'],
        ],
        'type'         => [
            'type'           => 'hasOne',
            'class'          => MaterialType::class,
            'method'         => HasOneColumn::METHOD_SELECT,
            'column_display' => 'display_name',
            'columns'        => [],
        ],
        'use_pattern'  => [
            'type'       => 'choice',
            'values'     => ['picture' => 'Picture', 'color' => 'Color'],
            'validators' => ['required'],
        ],
        'color'        => [
            'type'       => 'color',
            'validators' => [
                [
                    'required',
                    'when'       => function ($model) {
                        return $model->model_use_pattern === 'color';
                    },
                    'whenClient' => "function (attribute, value) {
                        return false;
                    }",
                ],
            ],
        ],
        'picture'      => [
            'type'       => 'file',
            'save_path'  => '@web/app/web/img/materials/',
            'validators' => [
                [
                    'required',
                    'when'       => function ($model) {
                        return $model->model_use_pattern === 'picture';
                    },
                    'whenClient' => "function (attribute, value) {
                        return false;
                    }",
                ],
                [
                    'file',
                    'extensions' => ['png', 'jpg', 'jpeg', 'bmp'],
                ],
            ],
        ],
        'price'        => [
            'type'       => 'number',
            'step'       => 0.01,
            'min'        => 0.01,
            'validators' => ['required'],
        ],
        'multiplier'   => [
            'type'       => 'number',
            'step'       => 0.01,
            'min'        => 0.01,
            'validators' => ['required'],
        ],
        'details'      => [
            'type'       => 'json',
            'validators' => ['required'],
        ],
    ],
];
