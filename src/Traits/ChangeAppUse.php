<?php

namespace Kriss\WebmanFilesystem\Traits;

/**
 * 替换 $this->app 的使用
 */
trait ChangeAppUse
{
    /**
     * @inheritDoc
     */
    protected function getConfig($name)
    {
        return $this->filesystemConfig['disks'][$name] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getDefaultDriver()
    {
        return $this->filesystemConfig['default'] ?? 'local';
    }

    /**
     * @inheritDoc
     */
    public function getDefaultCloudDriver()
    {
        return $this->filesystemConfig['cloud'] ?? 'cloud';
    }
}