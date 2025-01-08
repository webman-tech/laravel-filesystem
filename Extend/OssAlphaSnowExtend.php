<?php

namespace WebmanTech\LaravelFilesystem\Extend;

use AlphaSnow\Flysystem\Aliyun\AliyunAdapter;
use AlphaSnow\Flysystem\Aliyun\AliyunFactory;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use support\Container;
use WebmanTech\LaravelFilesystem\Extend\OssAlphaSnow\FilesystemMacroManager;

/**
 * @link https://github.com/alphasnow/aliyun-oss-laravel/blob/4.x/src/AliyunServiceProvider.php
 */
class OssAlphaSnowExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createExtend(array $config): FilesystemAdapter
    {
        $config['url_prefixed'] = $config['url_prefixed'] ?? true;
        $client = Container::get(AliyunFactory::class)->createClient($config);
        $adapter = new AliyunAdapter($client, $config['bucket'], $config['prefix'] ?? '', $config);
        $driver = new Filesystem($adapter);
        $filesystem = new FilesystemAdapter($driver, $adapter, $config);
        (new FilesystemMacroManager($filesystem))->defaultRegister()->register($config['macros'] ?? []);
        return $filesystem;
    }
}
