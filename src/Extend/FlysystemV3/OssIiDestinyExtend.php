<?php

namespace WebmanTech\LaravelFilesystem\Extend\FlysystemV3;

use Iidestiny\Flysystem\Oss\OssAdapter;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;

/**
 * @link https://github.com/iiDestiny/laravel-filesystem-oss/blob/master/src/OssStorageServiceProvider.php
 */
class OssIiDestinyExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createFilesystemAdapter($config): FilesystemAdapter
    {
        $root = $config['root'] ?? null;
        $buckets = $config['buckets'] ?? [];

        $adapter = new OssAdapter(
            $config['access_key'],
            $config['secret_key'],
            $config['endpoint'],
            $config['bucket'],
            $config['isCName'],
            $root,
            $buckets
        );

        return new FilesystemAdapter(new Filesystem($adapter), $adapter, $config);
    }
}