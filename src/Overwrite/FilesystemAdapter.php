<?php

namespace Kriss\WebmanFilesystem\Overwrite;

use Illuminate\Filesystem\AwsS3V3Adapter;
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
        if (interface_exists(FilesystemInterface::class)) {
            // flysystem v1 版本
            if (class_exists(AwsS3V3Adapter::class)) {
                // laravel 9 添加了该类
                throw new InvalidArgumentException('illuminate/filesystem<9 only support league/flysystem<=1');
            }
            return new self($filesystemAdapter->getDriver());
        }
        if (interface_exists(FilesystemOperator::class)) {
            // flysystem v2v3 版本
            if (!class_exists(AwsS3V3Adapter::class)) {
                // laravel 9 添加了该类
                throw new InvalidArgumentException('league/flysystem>1 need illuminate/filesystem>=9');
            }
            return new self($filesystemAdapter->getDriver(), $filesystemAdapter->getAdapter(), $filesystemAdapter->getConfig());
        }

        throw new InvalidArgumentException('not support league/flysystem version');
    }
}