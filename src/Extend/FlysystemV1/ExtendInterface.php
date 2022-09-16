<?php

namespace WebmanTech\LaravelFilesystem\Extend\FlysystemV1;

use League\Flysystem\Filesystem;

interface ExtendInterface
{
    /**
     * @param array $config
     * @return Filesystem
     */
    public static function createFilesystem($config): Filesystem;
}