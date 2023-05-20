<?php

namespace WebmanTech\LaravelFilesystem\Facades;

use Illuminate\Contracts\Filesystem\Factory as FactoryContract;
use support\Container;
use WebmanTech\LaravelFilesystem\FilesystemManager;

/**
 * @method static \Illuminate\Contracts\Filesystem\Filesystem assertExists(string|array $path)
 * @method static \Illuminate\Contracts\Filesystem\Filesystem assertMissing(string|array $path)
 * @method static \Illuminate\Contracts\Filesystem\Filesystem cloud()
 * @method static \Illuminate\Contracts\Filesystem\Filesystem build(string|array $root)
 * @method static \Illuminate\Contracts\Filesystem\Filesystem disk(string|null $name = null)
 * @method static \Illuminate\Filesystem\FilesystemManager extend(string $driver, \Closure $callback)
 * @method static \Webman\Http\Response download(string $path, string|null $name = null, array|null $headers = [])
 * @method static \Webman\Http\Response response(string $path, string|null $name = null, array|null $headers = [], string|null $disposition = 'inline')
 * @method static array allDirectories(string|null $directory = null)
 * @method static array allFiles(string|null $directory = null)
 * @method static array directories(string|null $directory = null, bool $recursive = false)
 * @method static array files(string|null $directory = null, bool $recursive = false)
 * @method static bool append(string $path, string $data)
 * @method static bool copy(string $from, string $to)
 * @method static bool delete(string|array $paths)
 * @method static bool deleteDirectory(string $directory)
 * @method static bool exists(string $path)
 * @method static bool makeDirectory(string $path)
 * @method static bool missing(string $path)
 * @method static bool move(string $from, string $to)
 * @method static bool prepend(string $path, string $data)
 * @method static bool put(string $path, \Psr\Http\Message\StreamInterface|\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|resource $contents, mixed $options = [])
 * @method static bool setVisibility(string $path, string $visibility)
 * @method static bool writeStream(string $path, resource $resource, array $options = [])
 * @method static int lastModified(string $path)
 * @method static int size(string $path)
 * @method static resource|null readStream(string $path)
 * @method static string get(string $path)
 * @method static string getVisibility(string $path)
 * @method static string path(string $path)
 * @method static string temporaryUrl(string $path, \DateTimeInterface $expiration, array $options = [])
 * @method static string url(string $path)
 * @method static string|false mimeType(string $path)
 * @method static string|false putFile(string $path, \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string $file, mixed $options = [])
 * @method static string|false putFileAs(string $path, \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string $file, string $name, mixed $options = [])
 * @method static void macro(string $name, object|callable $macro)
 * @method static void buildTemporaryUrlsUsing(\Closure $callback)
 *
 * @see \Illuminate\Filesystem\FilesystemManager
 * @see \Illuminate\Support\Facades\Storage
 */
class Storage
{
    public static function instance(): FactoryContract
    {
        return Container::get(FilesystemManager::class);
    }

    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(...$arguments);
    }
}
