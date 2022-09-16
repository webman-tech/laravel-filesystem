<?php

namespace WebmanTech\LaravelFilesystem\Extend;

use WebmanTech\LaravelFilesystem\VersionHelper;

class CosExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createExtend($config)
    {
        if (!VersionHelper::isGteFlysystem3()) {
            return FlysystemV1\CosExtend::createFilesystem($config);
        }
        return FlysystemV3\CosExtend::createFilesystemAdapter($config);
    }
}