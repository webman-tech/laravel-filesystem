<?php

if (!function_exists('get_env')) {
    function get_env(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path() . '/test-storage', // 修改测试时的目录，方便清空
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path() . '/app/public',
            'url' => get_env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        's3' => [
            'driver' => 's3',
            'key' => get_env('AWS_ACCESS_KEY_ID'),
            'secret' => get_env('AWS_SECRET_ACCESS_KEY'),
            'region' => get_env('AWS_DEFAULT_REGION'),
            'bucket' => get_env('AWS_BUCKET'),
            'url' => get_env('AWS_URL'),
            'endpoint' => get_env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => get_env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],
        // add cloud
        'cloud' => [
            'driver' => 'local',
            'root' => storage_path() . '/app',
            'throw' => false,
        ],
        // add extend test
        'qiniu' => [
            // 配置与使用：https://github.com/overtrue/laravel-filesystem-qiniu
            'driver' => \WebmanTech\LaravelFilesystem\Extend\QiNiuOvertrueExtend::class,
            'access_key' => get_env('QINIU_ACCESS_KEY', 'xxxxxxxxxxxxxxxx'),
            'secret_key' => get_env('QINIU_SECRET_KEY', 'xxxxxxxxxxxxxxxx'),
            'bucket' => get_env('QINIU_BUCKET', 'test'),
            'domain' => get_env('QINIU_DOMAIN', 'xxx.clouddn.com'), // or host: https://xxxx.clouddn.com
        ],
        'cos' => [
            // 配置与使用：https://github.com/overtrue/laravel-filesystem-cos
            'driver' => \WebmanTech\LaravelFilesystem\Extend\CosOvertrueExtend::class,
            'app_id' => get_env('COS_APP_ID'),
            'secret_id' => get_env('COS_SECRET_ID'),
            'secret_key' => get_env('COS_SECRET_KEY'),
            'region' => get_env('COS_REGION', 'ap-guangzhou'),
            'bucket' => get_env('COS_BUCKET'),  // 不带数字 app_id 后缀
            // 可选，如果 bucket 为私有访问请打开此项
            'signed_url' => false,
            // 可选，是否使用 https，默认 false0
            'use_https' => true,
            // 可选，自定义域名
            'domain' => 'emample-12340000.cos.test.com',
            // 可选，使用 CDN 域名时指定生成的 URL host
            'cdn' => get_env('COS_CDN'),
            'prefix' => get_env('COS_PATH_PREFIX'), // 全局路径前缀
            'guzzle' => [
                'timeout' => get_env('COS_TIMEOUT', 60),
                'connect_timeout' => get_env('COS_CONNECT_TIMEOUT', 60),
            ],
        ],
        'oss' => \WebmanTech\LaravelFilesystem\VersionHelper::isGteLaravel9()
            ? [
                // 配置与使用：https://github.com/alphasnow/aliyun-oss-laravel
                'driver' => \WebmanTech\LaravelFilesystem\Extend\OssAlphaSnowExtend::class,
                // v4 的参数见：https://github.com/alphasnow/aliyun-oss-laravel/blob/4.x/config/config.php
                "access_key_id" => get_env("OSS_ACCESS_KEY_ID", 'xxx'),           // Required, YourAccessKeyId
                "access_key_secret" => get_env("OSS_ACCESS_KEY_SECRET", 'xxx'),       // Required, YourAccessKeySecret
                "bucket" => get_env("OSS_BUCKET", 'my-storage'),                  // Required, For example: my-bucket
                "endpoint" => get_env("OSS_ENDPOINT", 'oss-cn-shanghai.aliyuncs.com'),                // Required, For example: oss-cn-shanghai.aliyuncs.com
                "internal" => get_env("OSS_INTERNAL", null),          // Optional, For example: oss-cn-shanghai-internal.aliyuncs.com
                "domain" => get_env("OSS_DOMAIN", null),            // Optional, For example: oss.my-domain.com
                "prefix" => get_env("OSS_PREFIX", ""),              // Optional, The prefix of the store path
                "use_ssl" => get_env("OSS_SSL"),              // Optional, Whether to use HTTPS
                "reverse_proxy" => get_env("OSS_REVERSE_PROXY"),    // Optional, Whether to use the Reverse proxy, such as nginx
                "throw" => get_env("OSS_THROW"),            // Optional, Whether to throw an exception that causes an error
                "options" => [],                                 // Optional, Add global configuration parameters, For example: [\OSS\OssClient::OSS_CHECK_MD5 => false]
                "macros" => []                                  // Optional, Add custom Macro, For example: [\App\Macros\ListBuckets::class, \App\Macros\CreateBucket::class]
            ]
            : [
                // 配置与使用：https://github.com/alphasnow/aliyun-oss-laravel
                'driver' => \WebmanTech\LaravelFilesystem\Extend\OssAlphaSnowExtend::class,
                // v3 的参数见：https://github.com/alphasnow/aliyun-oss-laravel/blob/3.x/config/config.php
                'access_id' => get_env('OSS_ACCESS_ID', 'xxx'), // AccessKey ID, For example: LTAI4**************qgcsA
                'access_key' => get_env('OSS_ACCESS_KEY', 'xxx'), // AccessKey Secret, For example: PkT4F********************Bl9or
                'bucket' => get_env('OSS_BUCKET', 'my-storage'), // For example: my-storage
                'endpoint' => get_env('OSS_ENDPOINT', 'oss-cn-shanghai.aliyuncs.com'), // For example: oss-cn-shanghai.aliyuncs.com
                'internal' => get_env('OSS_INTERNAL'), // For example: oss-cn-shanghai-internal.aliyuncs.com
                'domain' => get_env('OSS_DOMAIN'), // For example: oss.my-domain.com
                'use_ssl' => get_env('OSS_USE_SSL', false), // Whether to use https
                'prefix' => get_env('OSS_PREFIX'), // The prefix of the store path
                'security_token' => get_env('OSS_TOKEN'),// Used by \OSS\OssClient
                'request_proxy' => get_env('OSS_PROXY'),// Used by \OSS\OssClient
                'use_domain_endpoint' => get_env('OSS_USE_DOMAIN_ENDPOINT', false), // Whether to upload using domain
                'signature_expires' => get_env('OSS_SIGNATURE_EXPIRES', '+60 minutes'), // The valid time of the temporary url
            ],
        'oss2' => [
            // 配置与使用：https://github.com/iiDestiny/laravel-filesystem-oss
            'driver' => \WebmanTech\LaravelFilesystem\Extend\OssIiDestinyExtend::class,
            'root' => '', // 设置上传时根前缀
            'access_key' => get_env('OSS_ACCESS_KEY', 'xxx'),
            'secret_key' => get_env('OSS_SECRET_KEY', 'xxx'),
            'endpoint' => get_env('OSS_ENDPOINT', 'oss-cn-shanghai.aliyuncs.com'), // 使用 ssl 这里设置如: https://oss-cn-beijing.aliyuncs.com
            'bucket' => get_env('OSS_BUCKET', 'my-storage'),
            'isCName' => get_env('OSS_IS_CNAME'), // 如果 isCname 为 false，endpoint 应配置 oss 提供的域名如：`oss-cn-beijing.aliyuncs.com`，否则为自定义域名，，cname 或 cdn 请自行到阿里 oss 后台配置并绑定 bucket
            // 如果有更多的 bucket 需要切换，就添加所有bucket，默认的 bucket 填写到上面，不要加到 buckets 中
            'buckets' => [
                'test' => [
                    'access_key' => get_env('OSS_ACCESS_KEY'),
                    'secret_key' => get_env('OSS_SECRET_KEY'),
                    'bucket' => get_env('OSS_TEST_BUCKET'),
                    'endpoint' => get_env('OSS_TEST_ENDPOINT'),
                    'isCName' => get_env('OSS_TEST_IS_CNAME'),
                ],
                //...
            ],
        ],
    ],
    'links' => [
        public_path() . '/storage' => storage_path() . '/app/public',
    ],
];
