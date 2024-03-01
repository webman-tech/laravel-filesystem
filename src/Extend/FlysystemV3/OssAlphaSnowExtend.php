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
        // 由于无法直接获取到 laravel 的版本，因此不能用版本比较的方式，此处改为默认为 true
        // 如果真的使用的 laravel 的版本小于 9.33.0，
        // 可以手动在 filesystems.php 中添加一个 'url_prefixed' => false 的配置
        //$config['url_prefixed'] = version_compare($app->version(), '9.33.0', '>=');
        $config['url_prefixed'] = $config['url_prefixed'] ?? true;

        $client = Container::get(AliyunFactory::class)->createClient($config);
        $adapter = new AliyunAdapter($client, $config['bucket'], $config['prefix'] ?? '', $config);
        $driver = new Filesystem($adapter);
        $filesystem = new FilesystemAdapter($driver, $adapter, $config);
        (new FilesystemMacroManager($filesystem))->defaultRegister()->register($config['macros'] ?? []);
        return $filesystem;
    }
}