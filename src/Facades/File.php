<?php

namespace WebmanTech\LaravelFilesystem\Facades;

use Illuminate\Filesystem\Filesystem;
use support\Container;

/**
 * @method static bool exists(string $path)
 * @method static bool missing(string $path)
 * @method static string get( $path, bool $lock = false)
 * @method static array json(string $path, int $flags = 0, bool $lock = false)
 * @method static string sharedGet( $path)
 * @method static mixed getRequire(string $path, array $data = [])
 * @method static mixed requireOnce(string $path, array $data = [])
 * @method static \Illuminate\Support\LazyCollection lines(string $path)
 * @method static string|false hash(string $path, string $algorithm = 'md5')
 * @method static int|bool put(string $path, string $contents, bool $lock = false)
 * @method static void replace(string $path, string $content, int|null $mode = null)
 * @method static void replaceInFile(array|string $search, array|string $replace, string $path)
 * @method static int prepend(string $path, string $data)
 * @method static int append(string $path, string $data, bool $lock = false)
 * @method static mixed chmod(string $path, int|null $mode = null)
 * @method static bool delete(string|array $paths)
 * @method static bool move(string $path, string $target)
 * @method static bool copy(string $path, string $target)
 * @method static bool|null link(string $target, string $link)
 * @method static void relativeLink(string $target, string $link)
 * @method static string name( $path)
 * @method static string basename( $path)
 * @method static string dirname( $path)
 * @method static string extension( $path)
 * @method static string|null guessExtension(string $path)
 * @method static string type( $path)
 * @method static string|false mimeType(string $path)
 * @method static int size(string $path)
 * @method static int lastModified(string $path)
 * @method static bool isDirectory(string $directory)
 * @method static bool isEmptyDirectory(string $directory,  $ignoreDotFiles = false)
 * @method static bool isReadable(string $path)
 * @method static bool isWritable(string $path)
 * @method static bool hasSameHash(string $firstFile, string $secondFile)
 * @method static bool isFile(string $file)
 * @method static array glob(string $pattern, int $flags = 0)
 * @method static \Symfony\Component\Finder\SplFileInfo[] files(string $directory, bool $hidden = false)
 * @method static \Symfony\Component\Finder\SplFileInfo[] allFiles(string $directory, bool $hidden = false)
 * @method static array directories(string $directory)
 * @method static void ensureDirectoryExists(string $path, int $mode = 0755, bool $recursive = true)
 * @method static bool makeDirectory(string $path, int $mode = 0755,  $recursive = false,  $force = false)
 * @method static bool moveDirectory(string $from, string $to,  $overwrite = false)
 * @method static bool copyDirectory(string $directory, string $destination, int|null $options = null)
 * @method static bool deleteDirectory(string $directory,  $preserve = false)
 * @method static bool deleteDirectories(string $directory)
 * @method static bool cleanDirectory(string $directory)
 * @method static \Illuminate\Filesystem\Filesystem|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Illuminate\Filesystem\Filesystem|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \Illuminate\Filesystem\Filesystem
 * @see \Illuminate\Support\Facades\File
 */
class File
{
    public static function instance(): Filesystem
    {
        return Container::get(Filesystem::class);
    }

    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(...$arguments);
    }
}
