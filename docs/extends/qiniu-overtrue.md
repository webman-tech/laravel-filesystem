# 安装依赖

```bash
# illuminate/filesystem < 9.0
composer require overtrue/flysystem-qiniu:~1
# illuminate/filesystem >= 9.0
composer require overtrue/flysystem-qiniu
```

# 配置

filesystems.php

```php
return [
    'default' => 'qiniu', // 切换默认
    'disks' => [
        'qiniu' => [
            // 配置与使用：https://github.com/overtrue/laravel-filesystem-qiniu
            'driver' => \WebmanTech\LaravelFilesystem\Extend\QiNiuOvertrueExtend::class,
            'access_key' => getenv('QINIU_ACCESS_KEY', 'xxxxxxxxxxxxxxxx'),
            'secret_key' => getenv('QINIU_SECRET_KEY', 'xxxxxxxxxxxxxxxx'),
            'bucket' => getenv('QINIU_BUCKET', 'test'),
            'domain' => getenv('QINIU_DOMAIN', 'xxx.clouddn.com'), // or host: https://xxxx.clouddn.com
        ],
    ],
]
```
