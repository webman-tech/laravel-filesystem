<?php

namespace WebmanTech\LaravelFilesystem\Extend;

use WebmanTech\LaravelFilesystem\VersionHelper;

class CosOvertrueExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createExtend($config)
    {
        if (!VersionHelper::isGteFlysystem3()) {
            return FlysystemV1\CosOvertrueExtend::createFilesystem($config);
        }
        return FlysystemV3\CosOvertrueExtend::createFilesystemAdapter($config);
    }
}