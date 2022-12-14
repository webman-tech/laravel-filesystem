<?php

namespace WebmanTech\LaravelFilesystem\Extend;

use WebmanTech\LaravelFilesystem\VersionHelper;

class OssAlphaSnowExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createExtend($config)
    {
        if (!VersionHelper::isGteFlysystem3()) {
            return FlysystemV1\OssAlphaSnowExtend::createFilesystem($config);
        }
        return FlysystemV3\OssAlphaSnowExtend::createFilesystemAdapter($config);
    }
}
