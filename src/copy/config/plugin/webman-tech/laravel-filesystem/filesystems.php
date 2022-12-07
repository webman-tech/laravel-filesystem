<?php

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path() . '/app',
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path() . '/app/public',
            'url' => getenv('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        's3' => [
            'driver' => 's3',
            'key' => getenv('AWS_ACCESS_KEY_ID'),
            'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
            'region' => getenv('AWS_DEFAULT_REGION'),
            'bucket' => getenv('AWS_BUCKET'),
            'url' => getenv('AWS_URL'),
            'endpoint' => getenv('AWS_ENDPOINT'),
            'use_path_style_endpoint' => getenv('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],
    ],
    'links' => [
        public_path() . '/storage' => storage_path() . '/app/public',
    ],
];