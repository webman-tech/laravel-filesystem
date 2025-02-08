# webman-tech/laravel-filesystem

> Split from [webman-tech/laravel-monorepo](https://github.com/webman-tech/laravel-monorepo)

Laravel [illuminate/filesystem](https://packagist.org/packages/illuminate/filesystem) for webman

## 介绍

站在巨人（laravel）的肩膀上使文件存储使用更加*可靠*和*便捷*

所有方法和配置与 laravel 几乎一模一样，因此使用方式完全参考 [Laravel文档](https://laravel.com/docs/filesystem) 即可

## 安装

```bash
composer require webman-tech/laravel-filesystem
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

原 Laravel 下通过 `$request()->file()` 之后的快捷文件操作，需要使用 [
`webman-tech/laravel-http`](https://github.com/webman-tech/laravel-http) 来支持

安装

```bash
composer require webman-tech/laravel-http
```

使用

```php
<?php

namespace app\controller;

use support\Request;
use WebmanTech\LaravelHttp\Facades\LaravelRequest;
use WebmanTech\LaravelHttp\Facades\LaravelUploadedFile;

class UserAvatarController
{
    public function update(Request $request)
    {
        $path = LaravelRequest::file('file')->store('avatars');
        // 或者
        $path = LaravelUploadedFile::wrapper($request->file('avatar'))->store('avatars');

        return response($path);
    }
}
```

### 自定义文件系统

通过在 `filesystems.php` 配置文件的 `disks` 中的 `driver` 直接使用驱动扩展类的 class 名即可（驱动扩展实现
`WebmanTech\LaravelFilesystem\Extend\ExtendInterface`）

目前提供以下非 Laravel 官方库支持的文件系统，可自行参考替换相应的实现

排名不分先后，不做具体推荐

| 厂商          | 扩展包                                                                              | 安装使用                                   |
|-------------|----------------------------------------------------------------------------------|----------------------------------------|
| QiNiu       | [overtrue/flysystem-qiniu](https://github.com/overtrue/laravel-filesystem-qiniu) | [文档](./docs/extends/qiniu-overtrue.md) |
| Tencent COS | [overtrue/flysystem-cos](https://github.com/overtrue/laravel-filesystem-cos)     | [文档](./docs/extends/cos-overtrue.md)   |
| Aliyun OSS  | [alphasnow/aliyun-oss-laravel](https://github.com/alphasnow/aliyun-oss-laravel)  | [文档](./docs/extends/oss-alphasnow.md)  |
