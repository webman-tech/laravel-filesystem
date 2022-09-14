# kriss/webman-filesystem

Laravel [illuminate/filesystem](https://packagist.org/packages/illuminate/filesystem) for webman

## 介绍

站在巨人（laravel）的肩膀上使文件存储使用更加*可靠*和*便捷*

所有方法和配置与 laravel 几乎一模一样，因此使用方式完成参考 [Laravel文档](http://laravel.p2hp.com/cndocs/8.x/filesystem) 即可

## 安装

> 由于 laravel 9 升级了 league/flysystem 到 3.x，详见[Laravel9升级说明](http://laravel.p2hp.com/cndocs/9.x/upgrade#flysystem-3)
，低于 larval 9 的版本需要使用 league/flysystem 1.x 的版本
，因此安装该依赖需要手动安装 `illuminate/filesystem` 和 `league/flysystem`

1. 安装 `kriss/webman-filesystem` 和 `illuminate/filesystem`

```bash
composer require kriss/webman-filesystem illuminate/filesystem
```

2. 安装 `league/flysystem`

根据 `illuminate/filesystem` 安装后的版本

```bash
# illuminate/filesystem < 9.0
composer require kriss/webman-filesystem:~1.1
# illuminate/filesystem < 9.0
composer require kriss/webman-filesystem:~3.0
```

## 使用

所有 API 同 laravel，以下仅对有些特殊的操作做说明

### 目录权限问题

Unix 系统下需要给予 `storage/app` 目录写权限

### Facade 入口

使用 `Kriss\WebmanFilesystem\Facades\File` 代替 `Illuminate\Support\Facades\File`

使用 `Kriss\WebmanFilesystem\Facades\Storage` 代替 `Illuminate\Support\Facades\Storage`

### 建立软链

```bash
php webman storage:link
```

> 建立软链之后建议将软链（如 `/public/storage`）加入根目录下的 `.gitignore` 中

> 同 Laravel，可以支持自定义建立多个对外的路劲软链

### 文件上传

TODO

### 自定义文件系统

TODO