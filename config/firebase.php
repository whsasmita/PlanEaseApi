<?php

return [
    'default' => env('FIREBASE_DEFAULT', 'app'),

    'projects' => [
        'app' => [
            'credentials' => [
                'file' => env('FIREBASE_CREDENTIALS', base_path('config/firebase/firebase_credentials.json')),
            ],
        ],
    ],
];