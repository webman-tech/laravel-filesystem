<?php

namespace Kriss\WebmanFilesystem\Extend;

use Kriss\WebmanFilesystem\VersionHelper;

class QiNiuExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createExtend($config)
    {
        if (!VersionHelper::isGteFlysystem3()) {
            return FlysystemV1\QiNiuExtend::createFilesystem($config);
        }
        return FlysystemV3\QiNiuExtend::createFilesystemAdapter($config);
    }
}