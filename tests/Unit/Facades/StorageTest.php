<?php

use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Webman\Http\Response;
use WebmanTech\LaravelFilesystem\Facades\File;
use WebmanTech\LaravelFilesystem\Facades\Storage;
use WebmanTech\LaravelFilesystem\FilesystemManager;
use WebmanTech\LaravelFilesystem\VersionHelper;

beforeEach(function () {
    $this->testPath = normalizePath(storage_path('test-storage'));
    @mkdir($this->testPath);
});

afterEach(function () {
    File::deleteDirectory($this->testPath);
});

test('instance', function () {
    expect(Storage::instance())->toBeInstanceOf(FilesystemManager::class);
});

test('filesystem manager function', function () {
    // disk
    expect(Storage::disk())->toBeInstanceOf(Filesystem::class);
    expect(Storage::disk())->toBeInstanceOf(FilesystemAdapter::class);
    $disk = Storage::disk('public');
    expect($disk)->toBeInstanceOf(FilesystemAdapter::class);
    expect(normalizePath($disk->path('a.txt')))->toEqual(normalizePath(storage_path() . '/app/public/a.txt'));

    // cloud
    expect(Storage::cloud())->toBeInstanceOf(Cloud::class);

    // build 单独测试
});

test('filesystem manager build', function () {
    try {
        expect(Storage::build(storage_path() . '/app'))->toBeInstanceOf(Filesystem::class);
    } catch (Throwable $e) {
        if (strpos($e->getMessage(), 'Call to undefined method') !== false) {
            $this->markTestSkipped('>= 8 才有该方法');
        }
    }
});

test('filesystem function', function () {
    // exists/missing
    $filename = 'exists.txt';
    expect(Storage::exists($filename))->toBeFalse();
    expect(Storage::missing($filename))->toBeTrue();

    // path
    $filename = 'path.txt';
    expect(normalizePath(Storage::path($filename)))->toEqual($this->testPath . '/' . $filename);

    // get 获取内容
    $filename = 'get.txt';
    try {
        Storage::get($filename);
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(FileNotFoundException::class);
    }
    file_put_contents($this->testPath . '/' . $filename, 'ok');
    expect(Storage::get($filename))->toEqual('ok');

    // response 创建一个文件流
    $filename = 'response.txt';
    $content = 'response';
    file_put_contents($this->testPath . '/' . $filename, $content);
    expect(Storage::response($filename))->toBeInstanceOf(Response::class);
    // 已替换为 webmam 的 response
    expect(Storage::response($filename)->rawBody())->toEqual($content);

    // download 下载文件
    $filename = 'download.txt';
    $content = 'download';
    file_put_contents($this->testPath . '/' . $filename, $content);
    expect(Storage::download($filename))->toBeInstanceOf(Response::class);
    // 已替换为 webmam 的 response
    expect(Storage::download($filename)->getHeader('Content-Disposition'))->toEqual('attachment; filename="' . $filename . '"');

    // 指定文件名
    $newFilename = 'new.txt';
    expect(Storage::download($filename, $newFilename)->getHeader('Content-Disposition'))->toEqual('attachment; filename="' . $newFilename . '"');

    // put 写入文件
    $filename = 'put.txt';
    $content = 'put';
    expect((bool)Storage::put($filename, $content))->toBeTrue();
    // 返回值不一定为 bool
    expect(file_get_contents($this->testPath . '/' . $filename))->toEqual($content);

    // putFile 写入文件到某个目录
    $filename = 'putFile.txt';
    $content = 'putFile';
    $localFile = $this->testPath . '/' . $filename;
    file_put_contents($localFile, $content);
    $path = 'putFile';

    // file 是本地文件路劲
    $file = $localFile;
    $savePath = Storage::putFile($path, $file);
    expect($savePath)->toStartWith($path . '/');
    // 返回值是最终的文件名
    expect(Storage::get($savePath))->toEqual($content);
    unlink($this->testPath . '/' . $savePath);

    // 写入的是 File
    $file = new \Illuminate\Http\File($localFile);
    $savePath = Storage::putFile($path, $file);
    expect(Storage::get($savePath))->toEqual($content);
    unlink($this->testPath . '/' . $savePath);

    // 写入的是 UploadFile
    $file = new UploadedFile($localFile, 'putFile.txt');
    $savePath = Storage::putFile($path, $file);
    expect(Storage::get($savePath))->toEqual($content);
    unlink($this->testPath . '/' . $savePath);

    // putFileAs 写入文件到某个目录，并重命名
    $filename = 'putFileAs.txt';
    $content = 'putFileAs';
    $localFile = $this->testPath . '/' . $filename;
    file_put_contents($localFile, $content);
    $path = 'putFileAs';
    $savePath = Storage::putFileAs($path, $localFile, 'putFileAsNew.txt');
    expect($savePath)->toEqual($path . '/putFileAsNew.txt');
    // 返回值是最终的文件名
    expect(Storage::get($savePath))->toEqual($content);

    // getVisibility/setVisibility 可见性
    $filename = 'visibility.txt';
    file_put_contents($this->testPath . '/' . $filename, 'visibility');
    $visibility = Storage::getVisibility($filename);
    expect(['private', 'public'])->toContain($visibility);
    // local 受环境影响，结果可能不一致
    $toggleVisibility = $visibility == 'private' ? 'public' : 'private';
    expect(Storage::setVisibility($filename, $toggleVisibility))->toBeTrue();
    expect(['private', 'public'])->toContain($visibility);

    // local 受环境影响，结果可能不一致
    // prepend/append 前后追加
    $filename = 'pend.txt';
    file_put_contents($this->testPath . '/' . $filename, 'pend');
    expect(Storage::prepend($filename, 'pre'))->toBeTrue();
    $eol = PHP_EOL;
    expect(Storage::get($filename))->toEqual("pre{$eol}pend");
    expect(Storage::append($filename, 'ap'))->toBeTrue();
    expect(Storage::get($filename))->toEqual("pre{$eol}pend{$eol}ap");

    // delete 删除
    $filename = 'delete.txt';
    $filename2 = 'delete2.txt';
    expect(Storage::delete($filename))->toEqual(VersionHelper::isGteFlysystem3());
    // 删除不存在的文件，league/flysystem v3 时返回为 true
    Storage::put($filename, 'delete');
    expect(Storage::delete($filename))->toBeTrue();
    // 删除单个已存在的文件
    expect(Storage::exists($filename))->toBeFalse();
    Storage::put($filename, 'delete');
    Storage::put($filename2, 'delete');
    expect(Storage::delete([$filename, $filename2]))->toBeTrue();
    // 删除多个存在的文件
    Storage::put($filename, 'delete');
    expect(Storage::delete([$filename, $filename2]))->toEqual(VersionHelper::isGteFlysystem3());

    // 删除多个存在，有一个不存在时，league/flysystem v3 时返回为 true
    // copy 复制
    $filename = 'copy.txt';
    $filename2 = 'copy2.txt';
    $content = 'copy';
    Storage::put($filename, $content);
    expect(Storage::copy($filename, $filename2))->toBeTrue();
    expect(Storage::get($filename))->toEqual($content);
    expect(Storage::get($filename2))->toEqual($content);

    // move 移动
    $filename = 'move.txt';
    $filename2 = 'move2.txt';
    $content = 'move';
    Storage::put($filename, $content);
    expect(Storage::move($filename, $filename2))->toBeTrue();
    expect(Storage::exists($filename))->toBeFalse();
    expect(Storage::get($filename2))->toEqual($content);

    // size 文件大小
    $filename = 'size.txt';
    $content = 'size';
    Storage::put($filename, $content);
    expect(Storage::size($filename))->toEqual(strlen($content));

    // mimeType
    $filename = 'mimeType.txt';
    $content = 'mimeType';
    Storage::put($filename, $content);
    expect(Storage::mimeType($filename))->toEqual('text/plain');

    // lastModified 更新时间
    $filename = 'lastModified.txt';
    $content = 'lastModified';
    Storage::put($filename, $content);
    expect(Storage::lastModified($filename))->toEqual(filemtime($this->testPath . '/' . $filename));

    // url 地址
    $filename = 'url.txt';
    $content = 'url';
    Storage::put($filename, $content);
    expect(Storage::url($filename))->toEqual('/storage/' . $filename);

    // readStream 流式读
    $filename = 'readStream.txt';
    $content = 'readStream';
    Storage::put($filename, $content);
    $stream = Storage::readStream($filename);
    expect(is_resource($stream))->toBeTrue();
    expect(stream_get_contents($stream))->toEqual($content);

    // writeStream 流式写入
    $filename = 'writeStream.txt';
    $filename2 = 'writeStream2.txt';
    $content = 'writeStream';
    $localFile = $this->testPath . '/' . $filename;
    file_put_contents($localFile, $content);
    $fp = fopen($localFile, 'r');
    expect(Storage::writeStream($filename2, $fp))->toBeTrue();
    fclose($fp);
    expect(Storage::get($filename2))->toEqual($content);

    // temporaryUrl 临时 url
    $filename = 'temporaryUrl.txt';
    $content = 'temporaryUrl';
    Storage::put($filename, $content);
    try {
        Storage::temporaryUrl($filename, (new \DateTime())->add(new \DateInterval('PT10M')));
    } catch (Throwable $e) {
        // local 不支持
        expect($e->getMessage())->toEqual('This driver does not support creating temporary URLs.');
    }

    // files 列出目录下所有文件，默认不递归
    expect(Storage::files())->toBeArray();
    expect(Storage::files('not_exist'))->toEqual([]);
    // 不存在的目录会返回空数组
    expect(Storage::files(null, true))->toBeArray();

    // 递归
    // allFiles 列出所有文件，递归
    expect(Storage::allFiles())->toBeArray();

    // directories 列出所有目录，默认不递归
    expect(Storage::directories())->toBeArray();
    expect(Storage::directories('not_exist'))->toEqual([]);
    // 不存在的目录会返回空数组
    expect(Storage::directories(null, true))->toBeArray();

    // 递归
    // allDirectories 列出所有目录，递归
    expect(Storage::allDirectories())->toBeArray();

    // makeDirectory 创建目录
    $path = 'makeDirectory';
    expect(Storage::makeDirectory($path))->toBeTrue();
    expect(Storage::exists($path))->toBeTrue();
    expect(Storage::makeDirectory($path))->toBeTrue();
    // 已经存在的目录再次创建返回 true
    $path2 = 'makeDirectory2/depth';
    expect(Storage::makeDirectory($path2))->toBeTrue();
    expect(Storage::exists($path2))->toBeTrue();

    // deleteDirectory 删除目录
    $path = 'deleteDirectory';
    expect(Storage::deleteDirectory($path))->toEqual(VersionHelper::isGteFlysystem3());
    // 删除不存在的目录时，league/flysystem v3 时返回为 true
    Storage::makeDirectory($path);
    expect(Storage::deleteDirectory($path))->toBeTrue();
});

test('extend', function () {
    $map = array_filter([
        'qiniu' => \Overtrue\Flysystem\Qiniu\QiniuAdapter::class,
        'cos' => \Overtrue\Flysystem\Cos\CosAdapter::class,
        'oss' => \AlphaSnow\Flysystem\Aliyun\AliyunAdapter::class,
    ]);

    foreach ($map as $name => $instance) {
        $disk = Storage::disk($name);
        expect($disk)->toBeInstanceOf(FilesystemAdapter::class);
        expect($disk->getAdapter())->toBeInstanceOf($instance);
    }
});

function normalizePath(string $path)
{
    return str_replace('\\', '/', $path);
}

