<?php

namespace WebmanTech\LaravelFilesystem\Extend;

use WebmanTech\LaravelFilesystem\VersionHelper;

class OssExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createExtend($config)
    {
        if (!VersionHelper::isGteFlysystem3()) {
            return FlysystemV1\OssExtend::createFilesystem($config);
        }
        return FlysystemV3\OssExtend::createFilesystemAdapter($config);
    }
}
