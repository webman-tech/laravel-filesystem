<?php

namespace WebmanTech\LaravelFilesystem\Tests\Facades;

use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;
use Throwable;
use Webman\Http\Response;
use WebmanTech\LaravelFilesystem\Facades\File;
use WebmanTech\LaravelFilesystem\Facades\Storage;
use WebmanTech\LaravelFilesystem\FilesystemManager;
use WebmanTech\LaravelFilesystem\VersionHelper;

/**
 * https://laravel.com/docs/10.x/filesystem
 */
class StorageTest extends TestCase
{
    const TEST_PATH = 'test-storage'; // 同 config 下的配置

    protected $testPath = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->testPath = $this->normalizePath(storage_path() . '/' . self::TEST_PATH);
        @mkdir($this->testPath);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        File::deleteDirectory($this->testPath);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(FilesystemManager::class, Storage::instance());
    }

    public function testFilesystemManagerFunction()
    {
        // disk
        $this->assertInstanceOf(Filesystem::class, Storage::disk());
        $this->assertInstanceOf(FilesystemAdapter::class, Storage::disk());
        $disk = Storage::disk('public');
        $this->assertInstanceOf(FilesystemAdapter::class, $disk);
        $this->assertEquals($this->normalizePath(storage_path() . '/app/public/a.txt'), $this->normalizePath($disk->path('a.txt')));

        // cloud
        $this->assertInstanceOf(Cloud::class, Storage::cloud());

        // build 单独测试
    }

    public function testFilesystemManagerBuild()
    {
        try {
            $this->assertInstanceOf(Filesystem::class, Storage::build(storage_path() . '/app'));
        } catch (Throwable $e) {
            if (strpos($e->getMessage(), 'Call to undefined method') !== false) {
                $this->markTestSkipped('>= 8 才有该方法');
            }
        }
    }

    public function testFilesystemFunction()
    {
        // exists/missing
        $filename = 'exists.txt';
        $this->assertFalse(Storage::exists($filename));
        $this->assertTrue(Storage::missing($filename));

        // path
        $filename = 'path.txt';
        $this->assertEquals($this->testPath . '/' . $filename, $this->normalizePath(Storage::path($filename)));

        // get 获取内容
        $filename = 'get.txt';
        try {
            Storage::get($filename);
        } catch (Throwable $e) {
            $this->assertInstanceOf(FileNotFoundException::class, $e);
        }
        file_put_contents($this->testPath . '/' . $filename, 'ok');
        $this->assertEquals('ok', Storage::get($filename));

        // response 创建一个文件流
        $filename = 'response.txt';
        $content = 'response';
        file_put_contents($this->testPath . '/' . $filename, $content);
        $this->assertInstanceOf(Response::class, Storage::response($filename)); // 已替换为 webmam 的 response
        $this->assertEquals($content, Storage::response($filename)->rawBody());

        // download 下载文件
        $filename = 'download.txt';
        $content = 'download';
        file_put_contents($this->testPath . '/' . $filename, $content);
        $this->assertInstanceOf(Response::class, Storage::download($filename)); // 已替换为 webmam 的 response
        $this->assertEquals('attachment; filename="' . $filename . '"', Storage::download($filename)->getHeader('Content-Disposition'));
        // 指定文件名
        $newFilename = 'new.txt';
        $this->assertEquals('attachment; filename="' . $newFilename . '"', Storage::download($filename, $newFilename)->getHeader('Content-Disposition'));

        // put 写入文件
        $filename = 'put.txt';
        $content = 'put';
        $this->assertTrue((bool)Storage::put($filename, $content)); // 返回值不一定为 bool
        $this->assertEquals($content, file_get_contents($this->testPath . '/' . $filename));

        // putFile 写入文件到某个目录
        $filename = 'putFile.txt';
        $content = 'putFile';
        $localFile = $this->testPath . '/' . $filename;
        file_put_contents($localFile, $content);
        $path = 'putFile';
        // file 是本地文件路劲
        $file = $localFile;
        $savePath = Storage::putFile($path, $file);
        $this->assertStringStartsWith($path . '/', $savePath); // 返回值是最终的文件名
        $this->assertEquals($content, Storage::get($savePath));
        unlink($this->testPath . '/' . $savePath);
        // 写入的是 File
        $file = new \Illuminate\Http\File($localFile);
        $savePath = Storage::putFile($path, $file);
        $this->assertEquals($content, Storage::get($savePath));
        unlink($this->testPath . '/' . $savePath);
        // 写入的是 UploadFile
        $file = new UploadedFile($localFile, 'putFile.txt');
        $savePath = Storage::putFile($path, $file);
        $this->assertEquals($content, Storage::get($savePath));
        unlink($this->testPath . '/' . $savePath);

        // putFileAs 写入文件到某个目录，并重命名
        $filename = 'putFileAs.txt';
        $content = 'putFileAs';
        $localFile = $this->testPath . '/' . $filename;
        file_put_contents($localFile, $content);
        $path = 'putFileAs';
        $savePath = Storage::putFileAs($path, $localFile, 'putFileAsNew.txt');
        $this->assertEquals($path . '/putFileAsNew.txt', $savePath); // 返回值是最终的文件名
        $this->assertEquals($content, Storage::get($savePath));

        // getVisibility/setVisibility 可见性
        $filename = 'visibility.txt';
        file_put_contents($this->testPath . '/' . $filename, 'visibility');
        $visibility = Storage::getVisibility($filename);
        $this->assertContains($visibility, ['private', 'public']); // local 受环境影响，结果可能不一致
        $toggleVisibility = $visibility == 'private' ? 'public' : 'private';
        $this->assertTrue(Storage::setVisibility($filename, $toggleVisibility));
        $this->assertContains($visibility, ['private', 'public']); // local 受环境影响，结果可能不一致

        // prepend/append 前后追加
        $filename = 'pend.txt';
        file_put_contents($this->testPath . '/' . $filename, 'pend');
        $this->assertTrue(Storage::prepend($filename, 'pre'));
        $eol = PHP_EOL;
        $this->assertEquals("pre{$eol}pend", Storage::get($filename));
        $this->assertTrue(Storage::append($filename, 'ap'));
        $this->assertEquals("pre{$eol}pend{$eol}ap", Storage::get($filename));

        // delete 删除
        $filename = 'delete.txt';
        $filename2 = 'delete2.txt';
        $this->assertEquals(VersionHelper::isGteFlysystem3(), Storage::delete($filename)); // 删除不存在的文件，league/flysystem v3 时返回为 true
        Storage::put($filename, 'delete');
        $this->assertTrue(Storage::delete($filename)); // 删除单个已存在的文件
        $this->assertFalse(Storage::exists($filename));
        Storage::put($filename, 'delete');
        Storage::put($filename2, 'delete');
        $this->assertTrue(Storage::delete([$filename, $filename2])); // 删除多个存在的文件
        Storage::put($filename, 'delete');
        $this->assertEquals(VersionHelper::isGteFlysystem3(), Storage::delete([$filename, $filename2])); // 删除多个存在，有一个不存在时，league/flysystem v3 时返回为 true

        // copy 复制
        $filename = 'copy.txt';
        $filename2 = 'copy2.txt';
        $content = 'copy';
        Storage::put($filename, $content);
        $this->assertTrue(Storage::copy($filename, $filename2));
        $this->assertEquals($content, Storage::get($filename));
        $this->assertEquals($content, Storage::get($filename2));

        // move 移动
        $filename = 'move.txt';
        $filename2 = 'move2.txt';
        $content = 'move';
        Storage::put($filename, $content);
        $this->assertTrue(Storage::move($filename, $filename2));
        $this->assertFalse(Storage::exists($filename));
        $this->assertEquals($content, Storage::get($filename2));

        // size 文件大小
        $filename = 'size.txt';
        $content = 'size';
        Storage::put($filename, $content);
        $this->assertEquals(strlen($content), Storage::size($filename));

        // mimeType
        $filename = 'mimeType.txt';
        $content = 'mimeType';
        Storage::put($filename, $content);
        $this->assertEquals('text/plain', Storage::mimeType($filename));

        // lastModified 更新时间
        $filename = 'lastModified.txt';
        $content = 'lastModified';
        Storage::put($filename, $content);
        $this->assertEquals(filemtime($this->testPath . '/' . $filename), Storage::lastModified($filename));

        // url 地址
        $filename = 'url.txt';
        $content = 'url';
        Storage::put($filename, $content);
        $this->assertEquals('/storage/' . $filename, Storage::url($filename));

        // readStream 流式读
        $filename = 'readStream.txt';
        $content = 'readStream';
        Storage::put($filename, $content);
        $stream = Storage::readStream($filename);
        $this->assertTrue(is_resource($stream));
        $this->assertEquals($content, stream_get_contents($stream));

        // writeStream 流式写入
        $filename = 'writeStream.txt';
        $filename2 = 'writeStream2.txt';
        $content = 'writeStream';
        $localFile = $this->testPath . '/' . $filename;
        file_put_contents($localFile, $content);
        $fp = fopen($localFile, 'r');
        $this->assertTrue(Storage::writeStream($filename2, $fp));
        fclose($fp);
        $this->assertEquals($content, Storage::get($filename2));

        // temporaryUrl 临时 url
        $filename = 'temporaryUrl.txt';
        $content = 'temporaryUrl';
        Storage::put($filename, $content);
        try {
            Storage::temporaryUrl($filename, (new \DateTime())->add(new \DateInterval('PT10M')));
        } catch (Throwable $e) {
            // local 不支持
            $this->assertEquals('This driver does not support creating temporary URLs.', $e->getMessage());
        }

        // files 列出目录下所有文件，默认不递归
        $this->assertIsArray(Storage::files());
        $this->assertEquals([], Storage::files('not_exist')); // 不存在的目录会返回空数组
        $this->assertIsArray(Storage::files(null, true)); // 递归

        // allFiles 列出所有文件，递归
        $this->assertIsArray(Storage::allFiles());

        // directories 列出所有目录，默认不递归
        $this->assertIsArray(Storage::directories());
        $this->assertEquals([], Storage::directories('not_exist')); // 不存在的目录会返回空数组
        $this->assertIsArray(Storage::directories(null, true)); // 递归

        // allDirectories 列出所有目录，递归
        $this->assertIsArray(Storage::allDirectories());

        // makeDirectory 创建目录
        $path = 'makeDirectory';
        $this->assertTrue(Storage::makeDirectory($path));
        $this->assertTrue(Storage::exists($path));
        $this->assertTrue(Storage::makeDirectory($path)); // 已经存在的目录再次创建返回 true
        $path2 = 'makeDirectory2/depth';
        $this->assertTrue(Storage::makeDirectory($path2));
        $this->assertTrue(Storage::exists($path2));

        // deleteDirectory 删除目录
        $path = 'deleteDirectory';
        $this->assertEquals(VersionHelper::isGteFlysystem3(), Storage::deleteDirectory($path)); // 删除不存在的目录时，league/flysystem v3 时返回为 true
        Storage::makeDirectory($path);
        $this->assertTrue(Storage::deleteDirectory($path));
    }

    protected function normalizePath(string $path)
    {
        return str_replace('\\', '/', $path);
    }
}
