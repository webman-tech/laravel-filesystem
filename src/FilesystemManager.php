<?php

namespace WebmanTech\LaravelFilesystem;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\FilesystemManager as LaravelFilesystemManager;
use WebmanTech\LaravelFilesystem\Extend\ExtendInterface;
use WebmanTech\LaravelFilesystem\Helper\ConfigHelper;

class FilesystemManager extends LaravelFilesystemManager
{
    public function __construct()
    {
        $config = ConfigHelper::get('filesystems', []);

        // 从 disks 中提取 driver 作为自动扩展
        $autoExtends = collect($config['disks'] ?? [])
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
        $app = new LaravelApplication([
            'config' => new Repository([
                'filesystems' => $config,
            ]),
            // 用于 local 生成 temporaryUrl，原来使用的是 $app['url'] 组件
            // 但实际 webman 应该都不会装，因此不需要支持
            // 此处暂时留个可以扩展的口子
            'url' => $config['url_component'] ?? null,
        ]);
        parent::__construct($app);
    }

    /**
     * @inheritDoc
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
