<?php

namespace WebmanTech\LaravelFilesystem;

use Illuminate\Filesystem\FilesystemManager as LaravelFilesystemManager;
use WebmanTech\LaravelFilesystem\Extend\ExtendInterface;
use WebmanTech\LaravelFilesystem\Traits\ChangeAppUse;

class FilesystemManager extends LaravelFilesystemManager
{
    use ChangeAppUse;

    protected array $filesystemConfig = [];

    public function __construct()
    {
        $this->filesystemConfig = config('plugin.kriss.webman-filesystem.filesystems', []);
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
        $creator = $this->customCreators[$config['driver']];
        if (is_string($creator) && is_a($creator, ExtendInterface::class, true)) {
            return $creator::createExtend($config);
        }

        return parent::callCustomCreator($config);
    }
}