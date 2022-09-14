<?php

namespace Kriss\WebmanFilesystem\Extend;

use Kriss\WebmanFilesystem\VersionHelper;

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
