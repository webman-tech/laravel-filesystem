# 安装依赖

```bash
composer require alphasnow/aliyun-oss-laravel
```

# 配置

filesystems.php

```php
return [
    'default' => 'oss', // 切换默认
    'disks' => [
        'oss' => [
            // 配置与使用：https://github.com/alphasnow/aliyun-oss-laravel
            'driver' => \WebmanTech\LaravelFilesystem\Extend\OssAlphaSnowExtend::class,
            // v3 的参数见：https://github.com/alphasnow/aliyun-oss-laravel/blob/3.x/config/config.php
            // v4 的参数见：https://github.com/alphasnow/aliyun-oss-laravel/blob/4.x/config/config.php
            'access_id' => getenv('OSS_ACCESS_ID'), // AccessKey ID, For example: LTAI4**************qgcsA
            'access_key' => getenv('OSS_ACCESS_KEY'), // AccessKey Secret, For example: PkT4F********************Bl9or
            'bucket' => getenv('OSS_BUCKET'), // For example: my-storage
            'endpoint' => getenv('OSS_ENDPOINT'), // For example: oss-cn-shanghai.aliyuncs.com
            'internal' => getenv('OSS_INTERNAL', null), // For example: oss-cn-shanghai-internal.aliyuncs.com
            'domain' => getenv('OSS_DOMAIN', null), // For example: oss.my-domain.com
            'use_ssl' => getenv('OSS_USE_SSL', false), // Whether to use https
            'prefix' => getenv('OSS_PREFIX', null), // The prefix of the store path
            'security_token' => getenv('OSS_TOKEN', null),// Used by \OSS\OssClient
            'request_proxy' => getenv('OSS_PROXY', null),// Used by \OSS\OssClient
            'use_domain_endpoint' => getenv('OSS_USE_DOMAIN_ENDPOINT', false), // Whether to upload using domain
            'signature_expires' => getenv('OSS_SIGNATURE_EXPIRES', '+60 minutes'), // The valid time of the temporary url
        ],
    ],
]
```
