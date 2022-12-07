<?php

namespace WebmanTech\LaravelFilesystem\Extend;

use WebmanTech\LaravelFilesystem\VersionHelper;

class OssIiDestinyExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createExtend($config)
    {
        if (!VersionHelper::isGteFlysystem3()) {
            return FlysystemV1\OssIiDestinyExtend::createFilesystem($config);
        }
        return FlysystemV3\OssIiDestinyExtend::createFilesystemAdapter($config);
    }
}
