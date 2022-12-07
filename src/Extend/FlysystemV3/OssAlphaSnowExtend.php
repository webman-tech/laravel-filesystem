<?php

namespace WebmanTech\LaravelFilesystem\Extend\FlysystemV3;

use AlphaSnow\Flysystem\Aliyun\AliyunAdapter;
use AlphaSnow\Flysystem\Aliyun\AliyunFactory;
use AlphaSnow\LaravelFilesystem\Aliyun\FilesystemMacroManager;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use support\Container;

/**
 * @link https://github.com/alphasnow/aliyun-oss-laravel/blob/4.x/src/AliyunServiceProvider.php
 */
class OssAlphaSnowExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createFilesystemAdapter($config): FilesystemAdapter
    {
        //$config['url_prefixed'] = version_compare($app->version(), '9.33.0', '>=');
        $client = Container::get(AliyunFactory::class)->createClient($config);
        $adapter = new AliyunAdapter($client, $config['bucket'], $config['prefix'] ?? '', $config);
        $driver = new Filesystem($adapter);
        $filesystem = new FilesystemAdapter($driver, $adapter, $config);
        (new FilesystemMacroManager($filesystem))->defaultRegister()->register($config['macros'] ?? []);
        return $filesystem;
    }
}