<?php

namespace WebmanTech\LaravelFilesystem;

class VersionHelper
{
    /**
     * 是否是 illuminate/filesystem 9及以上版本
     * @return bool
     */
    public static function isGteLaravel9(): bool
    {
        return class_exists('Illuminate\Filesystem\AwsS3V3Adapter');
    }

    /**
     * 是否是 league/flysystem 2及以上版本
     * @return bool
     */
    public static function isGteFlysystem3(): bool
    {
        return interface_exists('League\Flysystem\FilesystemOperator');
    }
}