<?php

namespace WebmanTech\LaravelFilesystem\Extend;

use Illuminate\Filesystem\FilesystemAdapter;

interface ExtendInterface
{
    /**
     * @param array $config
     * @return FilesystemAdapter
     */
    public static function createExtend(array $config): FilesystemAdapter;
}
