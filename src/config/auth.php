<?php
return [
    'providers' => [
        'users' => [
            'driver' => 'prep',
            'host' => env('AUTH_HOST'),
        ]
    ],
    'enable_self_verify_token' => env('AUTH_ENABLE_SELF_VERIFY_TOKEN', false)
];
