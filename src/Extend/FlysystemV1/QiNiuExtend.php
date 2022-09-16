<?php

namespace WebmanTech\LaravelFilesystem\Extend\FlysystemV1;

use League\Flysystem\Filesystem;
use Overtrue\Flysystem\Qiniu\Plugins\FetchFile;
use Overtrue\Flysystem\Qiniu\Plugins\FileUrl;
use Overtrue\Flysystem\Qiniu\Plugins\PrivateDownloadUrl;
use Overtrue\Flysystem\Qiniu\Plugins\RefreshFile;
use Overtrue\Flysystem\Qiniu\Plugins\UploadToken;
use Overtrue\Flysystem\Qiniu\QiniuAdapter;

/**
 * @link https://github.com/overtrue/laravel-filesystem-qiniu/blob/1.0.2/src/QiniuStorageServiceProvider.php
 */
class QiNiuExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createFilesystem($config): Filesystem
    {
        $adapter = new QiniuAdapter(
            $config['access_key'], $config['secret_key'],
            $config['bucket'], $config['domain']
        );

        $flysystem = new Filesystem($adapter);

        $flysystem->addPlugin(new FetchFile());
        $flysystem->addPlugin(new UploadToken());
        $flysystem->addPlugin(new FileUrl());
        $flysystem->addPlugin(new PrivateDownloadUrl());
        $flysystem->addPlugin(new RefreshFile());

        return $flysystem;
    }
}