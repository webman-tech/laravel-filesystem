<?php

namespace WebmanTech\LaravelFilesystem;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\FilesystemManager as LaravelFilesystemManager;
use WebmanTech\LaravelFilesystem\Extend\ExtendInterface;
use WebmanTech\LaravelFilesystem\Helper\ConfigHelper;
use WebmanTech\LaravelFilesystem\Mock\FilesystemAdapter;
use WebmanTech\LaravelFilesystem\Mock\LaravelApp;

class FilesystemManager extends LaravelFilesystemManager
{
    public function __construct()
    {
        /**
         * @var array<string, mixed> $config
         */
        $config = ConfigHelper::get('filesystems', []);

        // 从 disks 中提取 driver 作为自动扩展
        /**
         * @var array<string, mixed> $disks
         */
        $disks = $config['disks'] ?? [];
        $autoExtends = collect($disks)
            ->pluck('driver')
            ->unique()
            ->filter(function (string $driver) {
                return class_exists($driver) && is_a($driver, ExtendInterface::class, true);
            })
            ->mapWithKeys(function (string $driver) {
                return [$driver => $driver];
            })
            ->all();
        $this->customCreators = array_merge($autoExtends, $this->filesystemConfig['extends'] ?? []);

        // 替换 app
        $app = new LaravelApp([
            'config' => new Repository([
                'filesystems' => $config,
            ]),
            // 用于 local 生成 temporaryUrl，原来使用的是 $app['url'] 组件
            // 但实际 webman 应该都不会装，因此不需要支持
            // 此处暂时留个可以扩展的口子
            'url' => $config['url_component'] ?? null,
        ]);
        /** @phpstan-ignore-next-line */
        parent::__construct($app);
    }

    /**
     * @inheritDoc
     * @param array<string, mixed>|null $config
     */
    protected function resolve($name, $config = null)
    {
        $v = parent::resolve($name, $config);
        if ($v instanceof \Illuminate\Filesystem\FilesystemAdapter) {
            return FilesystemAdapter::wrapper($v);
        }

        return $v;
    }

    /**
     * @inheritDoc
     * @param array<string, mixed> $config
     */
    protected function callCustomCreator(array $config)
    {
        // ExtendInterface 快速创建
        $creator = $this->customCreators[$config['driver']];
        if (is_string($creator) && is_a($creator, ExtendInterface::class, true)) {
            return $creator::createExtend($config);
        }

        return parent::callCustomCreator($config);
    }
}
