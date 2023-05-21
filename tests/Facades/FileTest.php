<?php

namespace WebmanTech\LaravelFilesystem\Tests\Facades;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Throwable;
use WebmanTech\LaravelFilesystem\Facades\File;

/**
 * @see Filesystem
 */
class FileTest extends TestCase
{
    private $testPath = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->testPath = storage_path() . 'test-file';
        File::ensureDirectoryExists($this->testPath);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        File::deleteDirectory($this->testPath);
        @rmdir(storage_path() . '/link'); // 移除link的目录
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Filesystem::class, File::instance());
    }

    public function testAllFunction()
    {
        // exist 判断文件存在
        $this->assertTrue(File::exists($this->testPath));

        // missing 与 exist 相反
        $this->assertFalse(File::missing($this->testPath));

        // get 读取内容
        $filename = $this->testPath . '/get.txt';
        try {
            File::get($filename);
        } catch (Throwable $e) {
            $this->assertInstanceOf(FileNotFoundException::class, $e);
        }
        file_put_contents($filename, 'ok');
        $this->assertEquals('ok', File::get($filename));
        // get lock 需要并发读来测试
        $this->assertEquals('ok', File::get($filename, true));

        // sharedGet 共享获取，同理需要并发测
        $this->assertEquals('ok', File::sharedGet($filename));

        // getRequire  require xxx.php 并返回
        $filename = $this->testPath . '/getRequire.txt';
        try {
            File::getRequire($filename);
        } catch (Throwable $e) {
            $this->assertInstanceOf(FileNotFoundException::class, $e);
        }
        file_put_contents($filename, "<?php\n return ['a' => 'b'];");
        $this->assertEquals(['a' => 'b'], File::getRequire($filename));

        // requireOnce 类 getRequire，使用 require_once
        $filename = $this->testPath . '/requireOnce.txt';
        file_put_contents($filename, "<?php\n return ['a' => 'b'];");
        $this->assertEquals(['a' => 'b'], File::requireOnce($filename)); // 第一次取到
        $this->assertTrue(File::requireOnce($filename)); // 第二次返回 true

        // lines 独立测试

        // hash 对文件进行 md5
        $filename = $this->testPath . '/hash.txt';
        try {
            $this->assertFalse(File::hash($filename));
        } catch (Throwable $e) {
            $this->assertStringContainsString('No such file or directory', $e->getMessage());
        }
        file_put_contents($filename, 'hash');
        $this->assertEquals(md5_file($filename), File::hash($filename));

        // put 覆盖文件内容
        $filename = $this->testPath . '/put.txt';
        $content = 'put';
        $this->assertEquals(strlen($content), File::put($filename, $content)); // 返回文件大小
        $this->assertEquals($content, file_get_contents($filename));
        // lock 需要并发测试

        // replace 替换全部内容，感觉类 put，不确定实际用途
        $filename = $this->testPath . '/replace.txt';
        $content = 'replace';
        File::replace($filename, $content); // 无返回结果
        $this->assertEquals($content, file_get_contents($filename));

        // replaceInFile 独立测试

        // prepend 在文件内容前增加
        $filename = $this->testPath . '/prepend.txt';
        File::prepend($filename, 'prepend'); // 不存在时直接写入
        $this->assertEquals('prepend', file_get_contents($filename));
        File::prepend($filename, 'my');
        $this->assertEquals('myprepend', file_get_contents($filename));

        // append 在文件内容后增加
        $filename = $this->testPath . '/append.txt';
        File::append($filename, 'append'); // 不存在时直接写入
        $this->assertEquals('append', file_get_contents($filename));
        File::append($filename, 'my');
        $this->assertEquals('appendmy', file_get_contents($filename));

        // chmod 修改权限的
        File::chmod($this->testPath); // 不测试，只测方法存在

        // delete 删除文件
        $filename = $this->testPath . '/delete.txt';
        $filename2 = $this->testPath . '/delete2.txt';
        $this->assertFalse(File::delete($filename)); // 删除不存在的文件
        file_put_contents($filename, 'delete');
        $this->assertTrue(File::delete($filename)); // 删除单个已存在的文件
        $this->assertFalse(file_exists($filename));
        file_put_contents($filename, 'delete');
        file_put_contents($filename2, 'delete');
        $this->assertTrue(File::delete([$filename, $filename2])); // 删除多个存在的文件
        file_put_contents($filename, 'delete');
        $this->assertFalse(File::delete([$filename, $filename2])); // 删除多个存在的文件中有一个失败

        // move 移动文件
        $filename = $this->testPath . '/move.txt';
        $filename2 = $this->testPath . '/move2.txt';
        file_put_contents($filename, 'move');
        $this->assertTrue(File::move($filename, $filename2));
        $this->assertFalse(file_exists($filename));
        $this->assertEquals('move', file_get_contents($filename2));

        // copy 复制文件
        $filename = $this->testPath . '/copy.txt';
        $filename2 = $this->testPath . '/copy2.txt';
        file_put_contents($filename, 'copy');
        $this->assertTrue(File::copy($filename, $filename2));
        $this->assertEquals('copy', file_get_contents($filename));
        $this->assertEquals('copy', file_get_contents($filename2));

        // link/relativeLink 单独测试

        // name/basename/dirname/extension 使用 pathinfo 获取信息
        $filename = $this->testPath . '/pathinfo.txt';
        file_put_contents($filename, 'pathinfo');
        $info = pathinfo($filename);
        $this->assertEquals(File::name($filename), $info['filename']);
        $this->assertEquals(File::basename($filename), $info['basename']);
        $this->assertEquals(File::dirname($filename), $info['dirname']);
        $this->assertEquals(File::extension($filename), $info['extension']);

        // guessExtension 猜测真实的 extension，需要安装 symfony/mime
        $filename = $this->testPath . '/guessExtension.png';
        file_put_contents($filename, 'guessExtension');
        $this->assertEquals('txt', File::guessExtension($filename));

        // type 同 filetype
        $filename = $this->testPath . '/type.txt';
        file_put_contents($filename, 'type');
        $this->assertEquals(filetype($filename), File::type($filename));

        // mimeType
        $filename = $this->testPath . '/mimeType.txt';
        file_put_contents($filename, 'mimeType');
        $this->assertEquals('text/plain', File::mimeType($filename));

        // size 文件大小
        $filename = $this->testPath . '/size.txt';
        file_put_contents($filename, 'size');
        $this->assertEquals(4, File::size($filename));

        // lastModified 最后修改时间 同 filemtime
        $filename = $this->testPath . '/filemtime.txt';
        file_put_contents($filename, 'filemtime');
        $this->assertEquals(filemtime($filename), File::lastModified($filename));

        // isDirectory 同 is_dir
        $this->assertTrue(File::isDirectory($this->testPath));

        // isReadable 同 is_readable
        $this->assertTrue(File::isWritable($this->testPath));

        // isWritable 同 is_writable
        $this->assertTrue(File::isReadable($this->testPath));

        // isFile 同 is_file
        $this->assertFalse(File::isFile($this->testPath));

        // glob 同 glob
        $this->assertCount(count(glob($this->testPath . '/*.*')), File::glob($this->testPath . '/*.*'));

        // files 获取给定目录下的所有文件
        $count = count(glob($this->testPath . '/*.*'));
        $this->assertCount($count, File::files($this->testPath));
        file_put_contents($this->testPath . '/.gitignore', '');
        $this->assertCount($count + 1, File::files($this->testPath, true)); // 包含 .gitignore

        // allFiles 获取给定目录下的所有文件，包含子目录
        $count = count(glob($this->testPath . '/*.*'));
        $this->assertCount($count, File::allFiles($this->testPath));
        mkdir($this->testPath . '/allFiles');
        file_put_contents($this->testPath . '/allFiles/newFile.txt', 'allFiles');
        $this->assertCount($count + 1, File::allFiles($this->testPath)); // 包含子目录下的

        // directories 获取给定目录下的所有子目录
        $this->assertCount(1, File::directories($this->testPath)); // 上一个测试中创建了1个子目录

        // ensureDirectoryExists 确保目录存在，递归
        $path = $this->testPath . '/ensureDirectoryExists';
        $path2 = $this->testPath . '/ensureDirectoryExists2/depth';
        File::ensureDirectoryExists($path);
        $this->assertTrue(is_dir($path));
        File::ensureDirectoryExists($path2); // 默认 recursive 为 true
        $this->assertTrue(is_dir($path2));

        // makeDirectory 新建目录，非递归
        $path = $this->testPath . '/makeDirectory';
        $path2 = $this->testPath . '/makeDirectory2/depth';
        File::makeDirectory($path);
        $this->assertTrue(is_dir($path));
        try {
            File::makeDirectory($path); // 创建已经存在的时报错
        } catch (Throwable $e) {
            $this->assertStringContainsString('File exists', $e->getMessage());
        }
        try {
            File::makeDirectory($path2); // 递归创建
        } catch (Throwable $e) {
            $this->assertStringContainsString('No such file or directory', $e->getMessage());
        }

        // moveDirectory 移动目录
        $path1 = $this->testPath . '/moveDirectory';
        $path2 = $this->testPath . '/moveDirectory2';
        $filename = 'moveDirectory.txt';
        $content = 'moveDirectory';
        // 测试 path2 不存在时移动目录
        mkdir($path1);
        file_put_contents($path1 . '/' . $filename, $content);
        File::moveDirectory($path1, $path2);
        $this->assertFalse(is_dir($path1)); // 移动后 path1 不存在
        $this->assertTrue(is_dir($path2));
        $this->assertEquals($content, file_get_contents($path2 . '/' . $filename));
        // 测试 path1 path2 都存在时移动
        mkdir($path1);
        file_put_contents($path1 . '/' . $filename, 'aaa');
        $this->assertFalse(File::moveDirectory($path2, $path1)); // 移动时不覆盖，移动失败
        $this->assertTrue(File::moveDirectory($path2, $path1, true)); // 移动时覆盖，移动成功
        $this->assertFalse(is_dir($path2)); // 移动后 path2 不存在
        $this->assertEquals($content, file_get_contents($path1 . '/' . $filename));

        // copyDirectory 复制目录
        $path1 = $this->testPath . '/copyDirectory';
        $path2 = $this->testPath . '/copyDirectory2';
        $filename = 'copyDirectory.txt';
        $content = 'copyDirectory';
        mkdir($path1);
        file_put_contents($path1 . '/' . $filename, $content);
        $this->assertTrue(File::copyDirectory($path1, $path2));
        $this->assertTrue(is_dir($path1));
        $this->assertTrue(is_dir($path2));
        $this->assertEquals($content, file_get_contents($path2 . '/' . $filename));

        // deleteDirectory 删除目录，递归，保留当前目录
        $path1 = $this->testPath . '/deleteDirectory';
        $filename = 'deleteDirectory.txt';
        // 测试全删
        mkdir($path1);
        file_put_contents($path1 . '/' . $filename, '1');
        $this->assertTrue(File::deleteDirectory($path1));
        $this->assertFalse(is_dir($path1));
        // 测试保留目录名
        mkdir($path1);
        file_put_contents($path1 . '/' . $filename, '1');
        $this->assertTrue(File::deleteDirectory($path1, true));
        $this->assertTrue(is_dir($path1));
        $this->assertFalse(is_file($path1 . '/' . $filename));
        rmdir($path1);
        // 测试删除非目录
        mkdir($path1);
        file_put_contents($path1 . '/' . $filename, '1');
        $this->assertFalse(File::deleteDirectory($path1 . '/' . $filename));

        // deleteDirectories 删除目录下的所有目录，不包含目录下的文件
        $path1 = $this->testPath . '/deleteDirectories';
        $path2 = $this->testPath . '/deleteDirectories/depth';
        $filename = 'deleteDirectories.txt';
        mkdir($path1);
        mkdir($path2);
        file_put_contents($path1 . '/' . $filename, '1');
        $this->assertTrue(File::deleteDirectories($path1));
        $this->assertTrue(is_dir($path1));
        $this->assertTrue(is_file($path1 . '/' . $filename));
        $this->assertFalse(is_dir($path2));
        $this->assertFalse(File::deleteDirectories($path1)); // 删除无二级目录时返回 false

        // cleanDirectory 清空目录下的所有文件，但保留目录
        $path1 = $this->testPath . '/cleanDirectory';
        $filename = 'cleanDirectory.txt';
        mkdir($path1);
        file_put_contents($path1 . '/' . $filename, '1');
        $this->assertTrue(File::cleanDirectory($path1));
        $this->assertTrue(is_dir($path1));
        $this->assertFalse(is_file($path1 . '/' . $filename));
    }

    public function testLink()
    {
        // link 建立软链
        $linkPath = storage_path() . '/link';
        file_put_contents($this->testPath . '/link.txt', 'link');
        File::link($this->testPath, $linkPath);
        $this->assertTrue(file_exists($linkPath . '/link.txt'));
        $this->assertEquals('link', file_get_contents($linkPath . '/link.txt'));
        @rmdir($linkPath); // windows
        @unlink($linkPath); // linux

        // relativeLink 类 link，使用相对路径
    }

    public function testLines()
    {
        // lines 逐行读取
        $filename = $this->testPath . '/lines.txt';
        try {
            File::lines($filename);
        } catch (Throwable $e) {
            if ($this->checkMethodNotExist($e)) {
                $this->markTestSkipped('>=8.0 才有该方法');
            }
            $this->assertInstanceOf(FileNotFoundException::class, $e);
        }
        file_put_contents($filename, "111\n222\n333");
        $excepted = ['111', '222', '333'];
        foreach (File::lines($filename) as $index => $line) {
            $this->assertEquals($excepted[$index], $line);
        }
    }

    public function testReplaceInFile()
    {
        // replaceInFile 替换文件中的部分内容
        $filename = $this->testPath . '/replaceInFile.txt';
        file_put_contents($filename, 'hahaha, MeMeMe');
        try {
            File::replaceInFile('xxx', 'yyy', $filename);
        } catch (Throwable $e) {
            if ($this->checkMethodNotExist($e)) {
                $this->markTestSkipped('>=8.0 才有该方法');
            }
        }
        File::replaceInFile('a', 'x', $filename);
        $this->assertEquals('hxhxhx, MeMeMe', file_get_contents($filename));
        File::replaceInFile(['x', 'e'], ['e', 'a'], $filename);
        $this->assertEquals('hahaha, MaMaMa', file_get_contents($filename));
    }

    protected function checkMethodNotExist(Throwable $e): bool
    {
        // Method Illuminate\Filesystem\Filesystem::lines does not exist.
        return strpos($e->getMessage(), 'Method') !== false
            && strpos($e->getMessage(), 'does not exist') !== false;
    }
}
