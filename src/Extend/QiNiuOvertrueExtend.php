<?php

namespace WebmanTech\LaravelFilesystem\Extend;

use WebmanTech\LaravelFilesystem\VersionHelper;

class QiNiuOvertrueExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createExtend($config)
    {
        if (!VersionHelper::isGteFlysystem3()) {
            return FlysystemV1\QiNiuOvertrueExtend::createFilesystem($config);
        }
        return FlysystemV3\QiNiuOvertrueExtend::createFilesystemAdapter($config);
    }
}