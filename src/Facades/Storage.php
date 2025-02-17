<?php

namespace WebmanTech\LaravelFilesystem\Facades;

use Illuminate\Contracts\Filesystem\Factory as FactoryContract;
use support\Container;
use WebmanTech\LaravelFilesystem\FilesystemManager;

/**
 * @method static \Illuminate\Filesystem\FilesystemAdapter drive(string|null $name = null)
 * @method static \Illuminate\Filesystem\FilesystemAdapter disk(string|null $name = null)
 * @method static \Illuminate\Filesystem\FilesystemAdapter cloud()
 * @method static \Illuminate\Filesystem\FilesystemAdapter build(string|array $config)
 * @method static \Illuminate\Filesystem\FilesystemAdapter createLocalDriver(array $config, string $name = 'local')
 * @method static \Illuminate\Filesystem\FilesystemAdapter createFtpDriver(array $config)
 * @method static \Illuminate\Filesystem\FilesystemAdapter createSftpDriver(array $config)
 * @method static \Illuminate\Filesystem\FilesystemAdapter createS3Driver(array $config)
 * @method static \Illuminate\Filesystem\FilesystemAdapter createScopedDriver(array $config)
 * @method static \Illuminate\Filesystem\FilesystemManager set(string $name, mixed $disk)
 * @method static string getDefaultDriver()
 * @method static string getDefaultCloudDriver()
 * @method static \Illuminate\Filesystem\FilesystemManager forgetDisk(array|string $disk)
 * @method static void purge(string|null $name = null)
 * @method static \Illuminate\Filesystem\FilesystemManager extend(string $driver, \Closure $callback)
 * @method static \Illuminate\Filesystem\FilesystemManager setApplication(\Illuminate\Contracts\Foundation\Application $app)
 * @method static string path( $path)
 * @method static bool exists(string $path)
 * @method static string|null get(string $path)
 * @method static resource|null readStream(string $path)
 * @method static bool put(string $path, \Psr\Http\Message\StreamInterface|\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|resource $contents, mixed $options = [])
 * @method static string|false putFile(\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string $path, \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|array|null $file = null, mixed $options = [])
 * @method static string|false putFileAs(\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string $path, \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|array|null $file, string|array|null $name = null, mixed $options = [])
 * @method static bool writeStream(string $path, resource $resource, array $options = [])
 * @method static string getVisibility( $path)
 * @method static bool setVisibility(string $path, string $visibility)
 * @method static bool prepend(string $path, string $data)
 * @method static bool append(string $path, string $data)
 * @method static bool delete(string|array $paths)
 * @method static bool copy(string $from, string $to)
 * @method static bool move(string $from, string $to)
 * @method static int size(string $path)
 * @method static int lastModified(string $path)
 * @method static array files(string|null $directory = null, bool $recursive = false)
 * @method static array allFiles(string|null $directory = null)
 * @method static array directories(string|null $directory = null, bool $recursive = false)
 * @method static array allDirectories(string|null $directory = null)
 * @method static bool makeDirectory(string $path)
 * @method static bool deleteDirectory(string $directory)
 * @method static \Illuminate\Filesystem\FilesystemAdapter assertExists(string|array $path, string|null $content = null)
 * @method static \Illuminate\Filesystem\FilesystemAdapter assertCount(string $path, int $count, bool $recursive = false)
 * @method static \Illuminate\Filesystem\FilesystemAdapter assertMissing(string|array $path)
 * @method static \Illuminate\Filesystem\FilesystemAdapter assertDirectoryEmpty(string $path)
 * @method static bool missing(string $path)
 * @method static bool fileExists(string $path)
 * @method static bool fileMissing(string $path)
 * @method static bool directoryExists(string $path)
 * @method static bool directoryMissing(string $path)
 * @method static array|null json(string $path, int $flags = 0)
 * @method static \Webman\Http\Response response(string $path, string|null $name = null, array $headers = [], string|null $disposition = 'inline')
 * @method static \Webman\Http\Response serve(\Illuminate\Http\Request $request, string $path, string|null $name = null, array $headers = [])
 * @method static \Webman\Http\Response download(string $path, string|null $name = null, array $headers = [])
 * @method static string|false checksum(string $path, array $options = [])
 * @method static string|false mimeType(string $path)
 * @method static string url( $path)
 * @method static bool providesTemporaryUrls()
 * @method static string temporaryUrl( $path, \DateTimeInterface $expiration, array $options = [])
 * @method static array temporaryUploadUrl(string $path, \DateTimeInterface $expiration,  $options = [])
 * @method static \League\Flysystem\FilesystemOperator getDriver()
 * @method static \League\Flysystem\FilesystemAdapter getAdapter()
 * @method static array getConfig()
 * @method static void serveUsing(\Closure $callback)
 * @method static void buildTemporaryUrlsUsing(\Closure $callback)
 * @method static \Illuminate\Filesystem\FilesystemAdapter|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Illuminate\Filesystem\FilesystemAdapter|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 * @method static bool has(string $location)
 * @method static string read( $location)
 * @method static \League\Flysystem\DirectoryListing listContents(string $location, bool $deep = false)
 * @method static int fileSize(string $path)
 * @method static string visibility( $path)
 * @method static void write(string $location, string $contents, array $config = [])
 * @method static void createDirectory(string $location, array $config = [])
 *
 * @see \Illuminate\Support\Facades\Storage
 * @see \Illuminate\Filesystem\FilesystemAdapter
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
