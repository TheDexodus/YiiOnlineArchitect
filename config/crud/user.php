<?php

use auth\models\AuthItem;
use auth\models\OAuth;
use auth\models\SmsRequest;
use auth\models\User;
use borales\extensions\phoneInput\PhoneInput;
use borales\extensions\phoneInput\PhoneInputValidator;
use crud\components\Model;

return [
    'class'   => User::class,
    'title'   => 'User',
    'actions' => [Model::ACTION_CREATE, Model::ACTION_VIEW, Model::ACTION_UPDATE, Model::ACTION_DELETE],
    'columns' => [
        'email'       => ['type' => 'text', 'validators' => ['required', 'email']],
        'first_name'  => ['type' => 'text', 'validators' => ['required']],
        'last_name'   => ['type' => 'text', 'validators' => ['required']],
        'password'    => [
            'type'       => 'password',
            'validators' => ['required'],
            'pattern'    => '^[A-Za-z0-9!#$%&\'()*+,\\-.\\/:;<=>?@[\\\\\]^_`{|}~"]+$',
            'setter'     => 'setPassword',
        ],
        'phone'       => [
            'type'       => 'text',
            'validators' => [PhoneInputValidator::class, 'required'],
            'widget'     => PhoneInput::class,
        ],
        'gender'      => [
            'type'       => 'choice',
            'validators' => ['required'],
            'values'     => [
                User::GENDER_MALE   => 'Male',
                User::GENDER_FEMALE => 'Female',
            ],
        ],
        'birthday'    => [
            'type'       => 'date',
            'validators' => ['required'],
            'min_date'   => '1900-01-01',
            'max_date'   => date('Y-m-d'),
        ],
        'sms_request' => [
            'type'          => 'hasOne',
            'class'         => SmsRequest::class,
            'target_column' => 'user_id',
            'columns'       => [
                'request_time' => ['type' => 'timestamp'],
                'attempt'      => ['type' => 'number', 'min' => 0],
            ],
        ],
        'o_auth'      => [
            'type'    => 'hasOne',
            'class'   => OAuth::class,
            'columns' => [
                'facebook' => ['type' => 'text'],
                'google'   => ['type' => 'text'],
            ],
        ],
        'auth_items'  => [
            'type'    => 'hasMany',
            'class'   => AuthItem::class,
            'columns' => [
                'name'        => ['type' => 'text'],
                'type'        => ['type' => 'number'],
                'description' => ['type' => 'text'],
            ],
        ],
    ],
];