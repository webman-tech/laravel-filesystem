<?php

namespace WebmanTech\LaravelFilesystem\Extend\OssAlphaSnow;

use AlphaSnow\Flysystem\Aliyun\AliyunException;
use AlphaSnow\LaravelFilesystem\Aliyun\Macros\AliyunMacro;
use Illuminate\Filesystem\FilesystemAdapter;
use support\Container;

/**
 * @internal
 */
class FilesystemMacroManager extends \AlphaSnow\LaravelFilesystem\Aliyun\FilesystemMacroManager
{
    public function __construct(FilesystemAdapter $filesystemAdapter)
    {
        $this->filesystemAdapter = $filesystemAdapter;
    }

    /**
     * @inheritDoc
     */
    public function register(array $macros): \AlphaSnow\LaravelFilesystem\Aliyun\FilesystemMacroManager
    {
        foreach ($macros as $macro) {
            $filesystemMacro = Container::make($macro, []); // change this app use
            if (!$filesystemMacro instanceof AliyunMacro) {
                throw new AliyunException("FilesystemMacroManager register want AliyunMacro, But got ".$filesystemMacro::class, 0);
            }

            $this->filesystemAdapter::macro($filesystemMacro->name(), $filesystemMacro->macro());
        }
        return $this;
    }
}
