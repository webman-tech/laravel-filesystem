<?php

namespace WebmanTech\LaravelFilesystem\Helper;

use function WebmanTech\CommonUtils\config;

/**
 * @internal
 */
final class ConfigHelper
{
    /**
     * 获取配置
     * @param string $key
     * @param $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return config("plugin.webman-tech.laravel-filesystem.{$key}", $default);
    }
}
