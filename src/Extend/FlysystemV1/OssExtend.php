<?php

namespace Kriss\WebmanFilesystem\Extend\FlysystemV1;

use Iidestiny\Flysystem\Oss\OssAdapter;
use Iidestiny\Flysystem\Oss\Plugins\FileUrl;
use Iidestiny\Flysystem\Oss\Plugins\Kernel;
use Iidestiny\Flysystem\Oss\Plugins\SetBucket;
use Iidestiny\Flysystem\Oss\Plugins\SignatureConfig;
use Iidestiny\Flysystem\Oss\Plugins\SignUrl;
use Iidestiny\Flysystem\Oss\Plugins\TemporaryUrl;
use Iidestiny\Flysystem\Oss\Plugins\Verify;
use League\Flysystem\Filesystem;

/**
 * @link https://github.com/iiDestiny/laravel-filesystem-oss/blob/2.1/src/OssStorageServiceProvider.php
 */
class OssExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createFilesystem($config): Filesystem
    {
        $root = $config['root'] ?? null;
        $buckets = isset($config['buckets']) ? $config['buckets'] : [];
        $adapter = new OssAdapter(
            $config['access_key'],
            $config['secret_key'],
            $config['endpoint'],
            $config['bucket'],
            $config['isCName'],
            $root,
            $buckets
        );

        $filesystem = new Filesystem($adapter);

        $filesystem->addPlugin(new FileUrl());
        $filesystem->addPlugin(new SignUrl());
        $filesystem->addPlugin(new TemporaryUrl());
        $filesystem->addPlugin(new SignatureConfig());
        $filesystem->addPlugin(new SetBucket());
        $filesystem->addPlugin(new Verify());
        $filesystem->addPlugin(new Kernel());

        return $filesystem;
    }
}