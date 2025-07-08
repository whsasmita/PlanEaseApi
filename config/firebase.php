<?php

return [
    // 'default' => env('FIREBASE_DEFAULT', 'app'),

    // 'projects' => [
    //     'app' => [
    //         'credentials' => [
    //             'file' => env('FIREBASE_CREDENTIALS', base_path('config/firebase/firebase_credentials.json')),
    //         ],
    //     ],
    // ],
    'credentials' => storage_path('app/' . env('FIREBASE_CREDENTIALS_FILENAME')),
];
