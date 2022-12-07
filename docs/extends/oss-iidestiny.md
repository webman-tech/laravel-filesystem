# 安装依赖

```bash
# illuminate/filesystem < 9.0
composer require iidestiny/flysystem-oss:~2.7
# illuminate/filesystem >= 9.0
composer require iidestiny/flysystem-oss
```

# 配置

filesystems.php

```php
return [
    'default' => 'oss', // 切换默认
    'disks' => [
        'oss' => [
            // 配置与使用：https://github.com/iiDestiny/laravel-filesystem-oss
            'driver' => \WebmanTech\LaravelFilesystem\Extend\OssIiDestinyExtend::class,
            'root' => '', // 设置上传时根前缀
            'access_key' => getenv('OSS_ACCESS_KEY'),
            'secret_key' => getenv('OSS_SECRET_KEY'),
            'endpoint' => getenv('OSS_ENDPOINT'), // 使用 ssl 这里设置如: https://oss-cn-beijing.aliyuncs.com
            'bucket' => getenv('OSS_BUCKET'),
            'isCName' => getenv('OSS_IS_CNAME', false), // 如果 isCname 为 false，endpoint 应配置 oss 提供的域名如：`oss-cn-beijing.aliyuncs.com`，否则为自定义域名，，cname 或 cdn 请自行到阿里 oss 后台配置并绑定 bucket
            // 如果有更多的 bucket 需要切换，就添加所有bucket，默认的 bucket 填写到上面，不要加到 buckets 中
            'buckets' => [
                'test' => [
                    'access_key' => getenv('OSS_ACCESS_KEY'),
                    'secret_key' => getenv('OSS_SECRET_KEY'),
                    'bucket' => getenv('OSS_TEST_BUCKET'),
                    'endpoint' => getenv('OSS_TEST_ENDPOINT'),
                    'isCName' => getenv('OSS_TEST_IS_CNAME', false),
                ],
                //...
            ],
        ],
    ],
]
```
