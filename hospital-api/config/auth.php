<?php

return [
    'defaults' => [
        'guard'     => 'api',
        'passwords' => 'usuarios',
    ],

    'guards' => [
        'api' => [
            'driver'   => 'jwt',
            'provider' => 'usuarios',
        ],
    ],

    'providers' => [
        // Aponta para nosso model Usuario em vez do User padrão do Laravel
        'usuarios' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Usuario::class,
        ],
    ],

    'passwords' => [
        'usuarios' => [
            'provider' => 'usuarios',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
