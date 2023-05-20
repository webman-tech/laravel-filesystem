<?php

namespace WebmanTech\LaravelFilesystem\Tests\Facades;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use WebmanTech\LaravelFilesystem\Facades\File;

/**
 * @see Filesystem
 */
class FileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::ensureDirectoryExists(storage_path() . '/test');
        file_put_contents(storage_path() . '/test/.gitignore', '');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        File::deleteDirectory(storage_path(). '/test');
        @rmdir(storage_path() . '/link');
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Filesystem::class, File::instance());
    }

    public function testCommonFn()
    {
        // storage_path 路径
        $this->assertEquals($this->normalizePath($this->getStoragePath()), $this->normalizePath(storage_path()));

        // exist 判断文件存在
        $this->assertTrue(File::exists(storage_path() . '/test'));
        $this->assertFalse(File::exists(storage_path() . '/not_exist'));

        // missing 与 exist 相反
        $this->assertFalse(File::missing(storage_path() . '/test'));
        $this->assertTrue(File::missing(storage_path() . '/not_exist'));

        // get 读取内容
        $filename = storage_path() . '/test/get.txt';
        try {
            File::get($filename);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(FileNotFoundException::class, $e);
        }
        file_put_contents($filename, 'ok');
        $this->assertEquals('ok', File::get($filename));
        // get lock 需要并发读来测试，不太好模拟

        // sharedGet 共享获取，同理需要并发测

        // getRequire  require xxx.php 并返回
        $filename = storage_path() . '/test/getRequire.txt';
        try {
            File::getRequire($filename);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(FileNotFoundException::class, $e);
        }
        file_put_contents($filename, "<?php\n return ['a' => 'b'];");
        $this->assertEquals(['a' => 'b'], File::getRequire($filename));

        // requireOnce 类 getRequire，使用 require_once

        // lines 逐行读取：yield 实现的
        $filename = storage_path() . '/test/lines.txt';
        try {
            File::lines($filename);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(FileNotFoundException::class, $e);
        }
        file_put_contents($filename, "111\n222\n333");
        $excepted = ['111', '222', '333'];
        foreach (File::lines($filename) as $index => $line) {
            $this->assertEquals($excepted[$index], $line);
        }

        // hash 对文件进行 md5
        $filename = storage_path() . '/test/hash.txt';
        try {
            $this->assertFalse(File::hash($filename));
        } catch (\Throwable $e) {
            $this->assertStringContainsString('No such file or directory', $e->getMessage());
        }
        file_put_contents($filename, 'hash');
        $this->assertEquals(md5_file($filename), File::hash($filename));

        // put 覆盖文件内容
        $filename = storage_path() . '/test/put.txt';
        $content = 'put';
        $this->assertEquals(strlen($content), File::put($filename, $content));
        $this->assertEquals($content, file_get_contents($filename));
        // lock 需要并发测试

        // replace 替换全部内容，感觉类 put，不确定实际用途

        // replaceInFile 替换文件中的部分内容
        $filename = storage_path() . '/test/replaceInFile.txt';
        file_put_contents($filename, 'hahaha, MeMeMe');
        try {
            File::replaceInFile('xxx', 'yyy', $filename);
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), 'does not exist') !== false) {
                $this->markTestSkipped('>=8.0 才有该方法');
            }
        }
        File::replaceInFile('a', 'x', $filename);
        $this->assertEquals('hxhxhx, MeMeMe', file_get_contents($filename));
        File::replaceInFile(['x', 'e'], ['e', 'a'], $filename);
        $this->assertEquals('hahaha, MaMaMa', file_get_contents($filename));

        // prepend 在文件内容前增加
        $filename = storage_path() . '/test/prepend.txt';
        File::prepend($filename, 'prepend'); // 不存在时直接写入
        $this->assertEquals('prepend', file_get_contents($filename));
        File::prepend($filename, 'my');
        $this->assertEquals('myprepend', file_get_contents($filename));

        // append 在文件内容后增加
        $filename = storage_path() . '/test/append.txt';
        File::append($filename, 'append'); // 不存在时直接写入
        $this->assertEquals('append', file_get_contents($filename));
        File::append($filename, 'my');
        $this->assertEquals('appendmy', file_get_contents($filename));

        // chmod 修改权限的

        // delete 删除文件
        $filename = storage_path() . '/test/delete.txt';
        $filename2 = storage_path() . '/test/delete2.txt';
        $this->assertFalse(File::delete($filename)); // 删除不存在的文件
        file_put_contents($filename, 1);
        $this->assertTrue(File::delete($filename)); // 删除单个已存在的文件
        $this->assertFalse(file_exists($filename));
        file_put_contents($filename, 1);
        file_put_contents($filename2, 1);
        $this->assertTrue(File::delete([$filename, $filename2])); // 删除多个存在的文件
        file_put_contents($filename, 1);
        $this->assertFalse(File::delete([$filename, $filename2])); // 删除多个存在的文件中有一个失败

        // move 移动文件

        // copy 复制文件

        // link 建立软链
        file_put_contents(storage_path() . '/test/link.txt', 'link');
        File::link(storage_path() . '/test', storage_path() . '/link');
        $this->assertTrue(file_exists(storage_path() . '/link/link.txt'));
        $this->assertEquals('link', file_get_contents(storage_path() . '/link/link.txt'));

        // relativeLink 类 link，使用相对路径

        // name/basename/dirname/extension 使用 pathinfo 获取信息

        // guessExtension 猜测真实的 extension，需要安装 symfony/mime

        // type 同 filetype

        // mimeType

        // size 文件大小
        $filename = storage_path() . '/test/size.txt';
        file_put_contents($filename, 'size');
        $this->assertEquals(4, File::size($filename));

        // lastModified 最后修改时间 同 filemtime

        // isDirectory 同 is_dir

        // isReadable 同 is_readable

        // isWritable 同 is_writable

        // isFile 同 is_file

        // glob 同 glob

        // files 获取给定目录下的所有文件
        $path = storage_path() . '/test';
        $count = count(glob($path . '/*.*'));
        $this->assertCount($count, File::files($path));
        $this->assertCount($count + 1, File::files($path, true)); // 包含 .gitignore

        // allFiles 获取给定目录下的所有文件，包含子目录

        // directories 获取给定目录下的所有子目录
        $path = storage_path() . '/test';
        $this->assertCount(0, File::directories($path));

        // ensureDirectoryExists 确保目录存在，递归
        $path = storage_path() . '/test/ensureDirectoryExists';
        $path2 = storage_path() . '/test/ensureDirectoryExists2/depth';
        File::ensureDirectoryExists($path);
        $this->assertTrue(is_dir($path));
        File::ensureDirectoryExists($path2); // 默认 recursive 为 true
        $this->assertTrue(is_dir($path2));

        // makeDirectory 新建目录，非递归
        $path = storage_path() . '/test/makeDirectory';
        $path2 = storage_path() . '/test/makeDirectory2/depth';
        File::makeDirectory($path);
        $this->assertTrue(is_dir($path));
        try {
            File::makeDirectory($path); // 创建已经存在的时报错
        } catch (\Throwable $e) {
            $this->assertStringContainsString('File exists', $e->getMessage());
        }
        try {
            File::makeDirectory($path2); // 递归创建
        } catch (\Throwable $e) {
            $this->assertStringContainsString('No such file or directory', $e->getMessage());
        }

        // moveDirectory 移动目录

        // copyDirectory 复制目录

        // deleteDirectory 删除目录，非递归

        // deleteDirectories 删除目录，递归

        // cleanDirectory 清空目录下的所有文件，但保留目录
    }

    public function getStoragePath(): string
    {
        return dirname(__DIR__) . '/storage';
    }

    public function normalizePath(string $path)
    {
        return str_replace('\\', '/', $path);
    }
}
