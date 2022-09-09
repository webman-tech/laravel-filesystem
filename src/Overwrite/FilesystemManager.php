<?php

namespace Kriss\WebmanFilesystem\Overwrite;

use Illuminate\Filesystem\FilesystemManager as LaravelFilesystemManager;
use Kriss\WebmanFilesystem\Overwrite\Traits\ChangeAppUse;

class FilesystemManager extends LaravelFilesystemManager
{
    use ChangeAppUse;

    protected array $filesystemConfig = [];

    public function __construct()
    {
        $this->filesystemConfig = config('plugin/kriss/webman-filesystem/filesystems');
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
    public function createS3Driver(array $config)
    {
        return FilesystemAdapter::wrapper(parent::createS3Driver($config));
    }
}