<?php

namespace Kriss\WebmanFilesystem\Extend;

use Kriss\WebmanFilesystem\VersionHelper;

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