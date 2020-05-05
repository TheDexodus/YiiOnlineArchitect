<?php

return [
    'urls'            => [
        'login'               => 'auth/authentication/login',
        'register'            => 'auth/authentication/register',
        'logout'              => 'auth/authentication/logout',
        'phone/set'           => 'auth/authentication/enter-phone',
        'profile'             => 'auth/profile/index',
        'profile/<_a1>/<_a2>' => 'auth/profile/<_a1>-<_a2>',
        'password/restore'    => 'auth/restore/enter-email',
        'password/change'     => 'auth/restore/new-password',
        'confirm/<_a>'        => 'auth/authentication/confirm-<_a>',
    ],
    'confirm_phone' => false,
    'sms_repeat_send' => [
        // The key is the number of the attempt. The value is the timeout for the next attempt (in seconds).
        '0'  => 0,
        '1'  => 60,
        '2'  => 120,
        '3'  => 300,
        '4'  => 3600,
        '10' => 7200,
    ],
];