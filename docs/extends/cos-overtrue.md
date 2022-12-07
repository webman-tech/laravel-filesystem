# 安装依赖

```bash
# illuminate/filesystem < 9.0
composer require overtrue/flysystem-cos:~3
# illuminate/filesystem >= 9.0
composer require oovertrue/flysystem-cos
```

# 配置

filesystems.php

```php
return [
    'default' => 'cos', // 切换默认
    'disks' => [
         'cos' => [
            // 配置与使用：https://github.com/overtrue/laravel-filesystem-cos
            'driver' => \WebmanTech\LaravelFilesystem\Extend\CosOvertrueExtend::class,
            'app_id'     => getenv('COS_APP_ID'),
            'secret_id'  => getenv('COS_SECRET_ID'),
            'secret_key' => getenv('COS_SECRET_KEY'),
            'region'     => getenv('COS_REGION', 'ap-guangzhou'),
            'bucket'     => getenv('COS_BUCKET'),  // 不带数字 app_id 后缀
            // 可选，如果 bucket 为私有访问请打开此项
            'signed_url' => false,
            // 可选，是否使用 https，默认 false
            'use_https' => true,
            // 可选，自定义域名
            'domain' => 'emample-12340000.cos.test.com',
            // 可选，使用 CDN 域名时指定生成的 URL host
            'cdn' => getenv('COS_CDN'),
            'prefix' => getenv('COS_PATH_PREFIX'), // 全局路径前缀
            'guzzle' => [
                'timeout' => getenv('COS_TIMEOUT', 60),
                'connect_timeout' => getenv('COS_CONNECT_TIMEOUT', 60),
            ],
        ],
    ],
]
```
