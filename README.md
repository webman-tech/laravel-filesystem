# webman-tech/laravel-filesystem

> Split from [webman-tech/laravel-monorepo](https://github.com/webman-tech/laravel-monorepo)

适用于 webman 的 Laravel 文件系统组件，基于 illuminate/filesystem 实现。

## 安装

```bash
composer require webman-tech/laravel-filesystem
```

## 简介

该组件将 Laravel 强大的文件系统功能引入 webman 框架中，提供了统一的 API 来操作本地文件系统和云存储服务。

所有方法和配置与 Laravel 几乎一致，因此使用方式可完全参考 [Laravel Filesystem 文档](https://laravel.com/docs/filesystem)。

## 特殊使用说明

### 1. Facades 使用方式

- 使用 `WebmanTech\LaravelFilesystem\Facades\File` 替代 `Illuminate\Support\Facades\File`
- 使用 `WebmanTech\LaravelFilesystem\Facades\Storage` 替代 `Illuminate\Support\Facades\Storage`

### 2. 命令行工具

```
# 建立软链
php webman storage:link
```

### 3. 文件上传处理

原 Laravel 下通过 `$request->file()`
之后的快捷文件操作，需要使用 [webman-tech/laravel-http](https://github.com/webman-tech/laravel-http) 来支持：

```php
use WebmanTech\LaravelHttp\Facades\LaravelUploadedFile;

$path = LaravelUploadedFile::wrapper($request->file('avatar'))->store('avatars');
```

### 4. 自定义文件系统

通过在 `filesystems.php` 配置文件的 `disks` 中的 `driver` 直接使用驱动扩展类的 class 名即可（驱动扩展实现
`WebmanTech\LaravelFilesystem\Extend\ExtendInterface`）

目前提供以下非 Laravel 官方库支持的文件系统，可自行参考替换相应的实现

排名不分先后，不做具体推荐

| 厂商          | 扩展包                                                                              | 安装使用                                   |
|-------------|----------------------------------------------------------------------------------|----------------------------------------|
| QiNiu       | [overtrue/flysystem-qiniu](https://github.com/overtrue/laravel-filesystem-qiniu) | [文档](./docs/extends/qiniu-overtrue.md) |
| Tencent COS | [overtrue/flysystem-cos](https://github.com/overtrue/laravel-filesystem-cos)     | [文档](./docs/extends/cos-overtrue.md)   |
| Aliyun OSS  | [alphasnow/aliyun-oss-laravel](https://github.com/alphasnow/aliyun-oss-laravel)  | [文档](./docs/extends/oss-alphasnow.md)  |
