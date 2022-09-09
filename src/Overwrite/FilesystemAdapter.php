<?php

namespace Kriss\WebmanFilesystem\Overwrite;

use Illuminate\Filesystem\Cache;
use Illuminate\Filesystem\FilesystemAdapter as LaravelFilesystemAdapter;
use InvalidArgumentException;
use Kriss\WebmanFilesystem\Overwrite\Traits\ChangeHttpUse;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FilesystemOperator;

class FilesystemAdapter extends LaravelFilesystemAdapter
{
    use ChangeHttpUse;

    public static function wrapper(LaravelFilesystemAdapter $filesystemAdapter)
    {
        /**
         * laravel 8 -> 9
         * @link http://laravel.p2hp.com/cndocs/9.x/upgrade#flysystem-3
         */
        if (class_exists(FilesystemInterface::class)) {
            // flysystem v1 版本
            if (!class_exists(Cache::class)) {
                // laravel 9 移除了该类
                throw new InvalidArgumentException('illuminate/filesystem<9 only support league/flysystem<=1');
            }
            return new self($filesystemAdapter->getDriver());
        }
        if (class_exists(FilesystemOperator::class)) {
            // flysystem v2v3 版本
            if (class_exists(Cache::class)) {
                // laravel 9 移除了该类
                throw new InvalidArgumentException('league/flysystem>1 need illuminate/filesystem>=9');
            }
            return new self($filesystemAdapter->getDriver(), $filesystemAdapter->getAdapter(), $filesystemAdapter->getConfig());
        }

        throw new InvalidArgumentException('not support league/flysystem version');
    }
}