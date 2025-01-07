<?php

namespace WebmanTech\LaravelFilesystem\Extend;

use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Overtrue\Flysystem\Cos\CosAdapter;

/**
 * @link https://github.com/overtrue/laravel-filesystem-cos/blob/master/src/CosStorageServiceProvider.php
 */
class CosOvertrueExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createExtend(array $config): FilesystemAdapter
    {
        $adapter = new CosAdapter($config);

        return new FilesystemAdapter(new Filesystem($adapter), $adapter);
    }
}
