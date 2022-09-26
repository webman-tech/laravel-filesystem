# webman-tech/laravel-filesystem

Laravel [illuminate/filesystem](https://packagist.org/packages/illuminate/filesystem) for webman

## 介绍

站在巨人（laravel）的肩膀上使文件存储使用更加*可靠*和*便捷*

所有方法和配置与 laravel 几乎一模一样，因此使用方式完全参考 [Laravel文档](https://laravel.com/docs/8.x/filesystem) 即可

## 安装

> 由于 laravel 9 升级了 league/flysystem 到 3.x，详见[Laravel9升级说明](http://laravel.p2hp.com/cndocs/9.x/upgrade#flysystem-3)
，低于 larval 9 的版本需要使用 league/flysystem 1.x 的版本
，因此安装该依赖需要手动安装 `illuminate/filesystem` 和 `league/flysystem`

1. 安装 `webman-tech/laravel-filesystem`

```bash
composer require webman-tech/laravel-filesystem
```

2. 安装 `league/flysystem`

根据 `illuminate/filesystem` 安装后的版本（通过 `composer info illuminate/filesystem` 查看）

```bash
# illuminate/filesystem < 9.0
composer require league/flysystem:~1.1
# illuminate/filesystem >= 9.0
composer require league/flysystem
```

## 使用

所有 API 同 laravel，以下仅对有些特殊的操作做说明

### 目录权限问题

Unix 系统下需要给予 `storage/app` 目录写权限

### Facade 入口

使用 `WebmanTech\LaravelFilesystem\Facades\File` 代替 `Illuminate\Support\Facades\File`

使用 `WebmanTech\LaravelFilesystem\Facades\Storage` 代替 `Illuminate\Support\Facades\Storage`

### 建立软链

```bash
php webman storage:link
```

> 建立软链之后建议将软链（如 `/public/storage`）加入根目录下的 `.gitignore` 中

> 同 Laravel，可以支持自定义建立多个对外的路劲软链

### Request 文件上传

原 Laravel 下通过 `$request()->file()` 之后的快捷文件操作，需要使用 [`webman-tech/polyfill`](https://github.com/webman-tech/polyfill) 来支持

安装

```bash
composer require webman-tech/polyfill illuminate/http
```

使用

```bash
<?php

namespace app\controller;

use support\Request;
use WebmanTech\Polyfill\LaravelRequest;
use WebmanTech\Polyfill\LaravelUploadedFile;

class UserAvatarController
{
    public function update(Request $request)
    {
        $path = LaravelRequest::wrapper($request)->file('file')->store('avatars');
        // 或者
        $path = LaravelUploadedFile::wrapper($request->file('avatar'))->store('avatars');

        return response($path);
    }
}
```

### 自定义文件系统

通过在 `filesystems.php` 配置文件的 `extends` 中添加驱动（驱动可以通过实现 `WebmanTech\LaravelFilesystem\Extend\ExtendInterface` 来快速创建），
然后在 `disks` 中添加对应的实例即可 

目前提供以下非 Laravel 官方库支持的文件系统，可自行参考替换相应的实现

#### Aliyun OSS

使用 [iidestiny/flysystem-oss](https://github.com/iiDestiny/laravel-filesystem-oss)

```bash
# illuminate/filesystem < 9.0
composer require iidestiny/flysystem-oss:~2.7
# illuminate/filesystem >= 9.0
composer require iidestiny/flysystem-oss
```

#### QiNiu

使用 [overtrue/flysystem-qiniu](https://github.com/overtrue/laravel-filesystem-qiniu)

```bash
# illuminate/filesystem < 9.0
composer require overtrue/flysystem-qiniu:~1
# illuminate/filesystem >= 9.0
composer require overtrue/flysystem-qiniu
```

#### Tencent COS

使用 [overtrue/flysystem-cos](https://github.com/overtrue/laravel-filesystem-cos)

```bash
# illuminate/filesystem < 9.0
composer require overtrue/flysystem-cos:~3
# illuminate/filesystem >= 9.0
composer require oovertrue/flysystem-cos
```
