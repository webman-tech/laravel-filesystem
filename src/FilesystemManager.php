<?php

namespace WebmanTech\LaravelFilesystem;

use Illuminate\Filesystem\FilesystemManager as LaravelFilesystemManager;
use League\Flysystem\FilesystemInterface;
use WebmanTech\LaravelFilesystem\Extend\ExtendInterface;
use WebmanTech\LaravelFilesystem\Traits\ChangeAppUse;

class FilesystemManager extends LaravelFilesystemManager
{
    use ChangeAppUse;

    /**
     * @var array
     */
    protected $filesystemConfig = [];

    public function __construct()
    {
        $this->filesystemConfig = config('plugin.webman-tech.laravel-filesystem.filesystems', []);
        $this->customCreators = $this->filesystemConfig['extends'] ?? [];
        parent::__construct(null);
    }

    /**
     * @inheritDoc
     */
    public function createLocalDriver(array $config)
    {
        return FilesystemAdapter::wrapper(parent::createLocalDriver($config));
    }

    /**
     * @inheritDoc
     */
    public function createFtpDriver(array $config)
    {
        return FilesystemAdapter::wrapper(parent::createFtpDriver($config));
    }

    /**
     * @inheritDoc
     */
    public function createSftpDriver(array $config)
    {
        return FilesystemAdapter::wrapper(parent::createSftpDriver($config));
    }

    /**
     * @inheritDoc
     */
    public function createS3Driver(array $config)
    {
        return FilesystemAdapter::wrapper(parent::createS3Driver($config));
    }

    /**
     * @inheritDoc
     */
    protected function callCustomCreator(array $config)
    {
        $adapter = (function($config) {
            $creator = $this->customCreators[$config['driver']];
            if (is_string($creator) && is_a($creator, ExtendInterface::class, true)) {
                $driver = $creator::createExtend($config);
                if ($driver instanceof FilesystemInterface && method_exists($this, 'adapt')) {
                    return $this->adapt($driver);
                }
                return $driver;
            }

            return parent::callCustomCreator($config);
        })($config);
        return FilesystemAdapter::wrapper($adapter);
    }
}
