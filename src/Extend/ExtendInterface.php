<?php

namespace WebmanTech\LaravelFilesystem\Extend;

use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;

interface ExtendInterface
{
    /**
     * @param array $config
     * @return Filesystem|FilesystemAdapter
     */
    public static function createExtend($config);
}
