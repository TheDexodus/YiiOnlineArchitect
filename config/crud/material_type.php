<?php

use app\models\MaterialType;

return [
    'class'   => MaterialType::class,
    'title'   => 'Material Type',
    'columns' => [
        'display_name'        => [
            'type'       => 'text',
            'validators' => ['required'],
        ],
        //        'type' => [
        //            'type' => 'hasOne',
        //            'target_class' => MaterialType::class,
        //            'method' => 'select',
        //            'columns' => [],
        //        ],
        'usage'               => [
            'type'       => 'choice',
            'values'     => [
                MaterialType::USAGE_FLOORS => 'Floors',
                MaterialType::USAGE_WALLS  => 'Walls',
                MaterialType::USAGE_CELLS  => 'Ceils',
            ],
            'validators' => ['required'],
        ],
        'measurements'        => [
            'type'       => 'choice',
            'values'     => [
                MaterialType::MEASUREMENT_LITRES => 'Litres',
                MaterialType::MEASUREMENT_RUNNING_METRES  => 'Running metres',
                MaterialType::MEASUREMENT_KILOGRAMMES  => 'Kilogrammes',
            ],
            'validators' => ['required'],
        ],
        'typical_consumption' => [
            'type'       => 'choice',
            'values'     => [MaterialType::TYPICAL_CONSUMPTION_UNITS_PER_M2 => 'Units per square meter'],
            'validators' => ['required'],
        ],
    ],
];
