<?php

return [
    'enabled' => env('DEEPL_ENABLED', false),

    'auth_key' => env('DEEPL_AUTH_KEY'),

    'supported_locales' => ['en', 'pt', 'pl', 'ro'],

    'target_languages' => [
        'en' => 'EN',
        'pt' => 'PT',
        'pl' => 'PL',
        'ro' => 'RO',
    ],
];