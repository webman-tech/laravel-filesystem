<?php

namespace Kriss\WebmanFilesystem\Extend\FlysystemV3;

use Illuminate\Filesystem\FilesystemAdapter;

interface ExtendInterface
{
    /**
     * @param $config
     * @return FilesystemAdapter
     */
    public static function createFilesystemAdapter($config): FilesystemAdapter;
}
