<?php

if (base_path('/config/app.php')) {
    copy_dir(__DIR__. '/config', base_path('/config'));
}

if (!file_exists(storage_path('app'))) {
    \WebmanTech\LaravelFilesystem\Install::install();
}

require_once __DIR__ . '/../vendor/workerman/webman-framework/src/support/bootstrap.php';
