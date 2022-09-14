<?php

namespace Kriss\WebmanFilesystem\Extend\FlysystemV3;

use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Overtrue\Flysystem\Cos\CosAdapter;

/**
 * @link https://github.com/overtrue/laravel-filesystem-cos/blob/master/src/CosStorageServiceProvider.php
 */
class CosExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createFilesystemAdapter($config): FilesystemAdapter
    {
        $adapter = new CosAdapter($config);
        return new FilesystemAdapter(new Filesystem($adapter), $adapter);
    }
}