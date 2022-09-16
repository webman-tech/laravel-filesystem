<?php

namespace WebmanTech\LaravelFilesystem\Extend\FlysystemV1;

use League\Flysystem\Filesystem;
use Overtrue\Flysystem\Cos\CosAdapter;
use Overtrue\Flysystem\Cos\Plugins\FileSignedUrl;
use Overtrue\Flysystem\Cos\Plugins\FileUrl;

/**
 * @link https://github.com/overtrue/laravel-filesystem-cos/blob/2.0.0/src/CosStorageServiceProvider.php
 */
class CosExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createFilesystem($config): Filesystem
    {
        $adapter = new CosAdapter($config);

        $filesystem = new Filesystem($adapter);
        $filesystem->addPlugin(new FileUrl());
        $filesystem->addPlugin(new FileSignedUrl());

        return $filesystem;
    }
}