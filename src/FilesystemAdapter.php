<?php

namespace WebmanTech\LaravelFilesystem;

use Illuminate\Filesystem\FilesystemAdapter as LaravelFilesystemAdapter;
use InvalidArgumentException;
use WebmanTech\LaravelFilesystem\Traits\ChangeHttpUse;

class FilesystemAdapter extends LaravelFilesystemAdapter
{
    use ChangeHttpUse;

    public static function wrapper(LaravelFilesystemAdapter $filesystemAdapter)
    {
        if ($filesystemAdapter instanceof self) {
            return $filesystemAdapter;
        }

        /**
         * laravel 8 -> 9
         * @link http://laravel.p2hp.com/cndocs/9.x/upgrade#flysystem-3
         */
        if (!VersionHelper::isGteLaravel9()) {
            if (VersionHelper::isGteFlysystem3()) {
                throw new InvalidArgumentException('illuminate/filesystem<9 only support league/flysystem<=1');
            }
            return new self($filesystemAdapter->getDriver());
        }
        if (VersionHelper::isGteLaravel9()) {
            if (!VersionHelper::isGteFlysystem3()) {
                throw new InvalidArgumentException('illuminate/filesystem>=9 only support league/flysystem>=3');
            }
            return new self($filesystemAdapter->getDriver(), $filesystemAdapter->getAdapter(), $filesystemAdapter->getConfig());
        }

        throw new InvalidArgumentException('illuminate/filesystem version not matching league/flysystem version');
    }
}