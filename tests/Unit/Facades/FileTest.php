<?php

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use WebmanTech\LaravelFilesystem\Facades\File;


beforeEach(function () {
    $this->testPath = storage_path() . 'test-file';
    File::ensureDirectoryExists($this->testPath);
});

afterEach(function () {
    File::deleteDirectory($this->testPath);
});

test('instance', function () {
    expect(File::instance())->toBeInstanceOf(Filesystem::class);
});

test('all function', function () {
    // exist 判断文件存在
    expect(File::exists($this->testPath))->toBeTrue();

    // missing 与 exist 相反
    expect(File::missing($this->testPath))->toBeFalse();

    // get 读取内容
    $filename = $this->testPath . '/get.txt';
    try {
        File::get($filename);
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(FileNotFoundException::class);
    }
    file_put_contents($filename, 'ok');
    expect(File::get($filename))->toEqual('ok');

    // get lock 需要并发读来测试
    expect(File::get($filename, true))->toEqual('ok');

    // sharedGet 共享获取，同理需要并发测
    expect(File::sharedGet($filename))->toEqual('ok');

    // getRequire  require xxx.php 并返回
    $filename = $this->testPath . '/getRequire.php';
    try {
        File::getRequire($filename);
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(FileNotFoundException::class);
    }
    file_put_contents($filename, "<?php\n return ['a' => 'b'];");
    expect(File::getRequire($filename))->toEqual(['a' => 'b']);

    // requireOnce
    $filename = $this->testPath . '/requireOnce.php';
    file_put_contents($filename, "<?php\n function requireOnceFn() {}");
    File::requireOnce($filename);
    expect(function_exists('requireOnceFn'))->toBeTrue();

    // lines 独立测试
    // hash 对文件进行 md5
    $filename = $this->testPath . '/hash.txt';
    try {
        expect(File::hash($filename))->toBeFalse();
    } catch (Throwable $e) {
        $this->assertStringContainsString('No such file or directory', $e->getMessage());
    }
    file_put_contents($filename, 'hash');
    expect(File::hash($filename))->toEqual(md5_file($filename));

    // put 覆盖文件内容
    $filename = $this->testPath . '/put.txt';
    $content = 'put';
    expect(File::put($filename, $content))->toEqual(strlen($content));
    // 返回文件大小
    expect(file_get_contents($filename))->toEqual($content);

    // lock 需要并发测试
    // replace 替换全部内容，感觉类 put，不确定实际用途
    $filename = $this->testPath . '/replace.txt';
    $content = 'replace';
    File::replace($filename, $content);
    // 无返回结果
    expect(file_get_contents($filename))->toEqual($content);

    // replaceInFile 独立测试
    // prepend 在文件内容前增加
    $filename = $this->testPath . '/prepend.txt';
    File::prepend($filename, 'prepend');
    // 不存在时直接写入
    expect(file_get_contents($filename))->toEqual('prepend');
    File::prepend($filename, 'my');
    expect(file_get_contents($filename))->toEqual('myprepend');

    // append 在文件内容后增加
    $filename = $this->testPath . '/append.txt';
    File::append($filename, 'append');
    // 不存在时直接写入
    expect(file_get_contents($filename))->toEqual('append');
    File::append($filename, 'my');
    expect(file_get_contents($filename))->toEqual('appendmy');

    // chmod 修改权限的
    File::chmod($this->testPath);

    // 不测试，只测方法存在
    // delete 删除文件
    $filename = $this->testPath . '/delete.txt';
    $filename2 = $this->testPath . '/delete2.txt';
    expect(File::delete($filename))->toBeFalse();
    // 删除不存在的文件
    file_put_contents($filename, 'delete');
    expect(File::delete($filename))->toBeTrue();
    // 删除单个已存在的文件
    expect(file_exists($filename))->toBeFalse();
    file_put_contents($filename, 'delete');
    file_put_contents($filename2, 'delete');
    expect(File::delete([$filename, $filename2]))->toBeTrue();
    // 删除多个存在的文件
    file_put_contents($filename, 'delete');
    expect(File::delete([$filename, $filename2]))->toBeFalse();

    // 删除多个存在的文件中有一个失败
    // move 移动文件
    $filename = $this->testPath . '/move.txt';
    $filename2 = $this->testPath . '/move2.txt';
    file_put_contents($filename, 'move');
    expect(File::move($filename, $filename2))->toBeTrue();
    expect(file_exists($filename))->toBeFalse();
    expect(file_get_contents($filename2))->toEqual('move');

    // copy 复制文件
    $filename = $this->testPath . '/copy.txt';
    $filename2 = $this->testPath . '/copy2.txt';
    file_put_contents($filename, 'copy');
    expect(File::copy($filename, $filename2))->toBeTrue();
    expect(file_get_contents($filename))->toEqual('copy');
    expect(file_get_contents($filename2))->toEqual('copy');

    // link/relativeLink 单独测试
    // name/basename/dirname/extension 使用 pathinfo 获取信息
    $filename = $this->testPath . '/pathinfo.txt';
    file_put_contents($filename, 'pathinfo');
    $info = pathinfo($filename);
    expect($info['filename'])->toEqual(File::name($filename));
    expect($info['basename'])->toEqual(File::basename($filename));
    expect($info['dirname'])->toEqual(File::dirname($filename));
    expect($info['extension'])->toEqual(File::extension($filename));

    // guessExtension 猜测真实的 extension，需要安装 symfony/mime
    $filename = $this->testPath . '/guessExtension.png';
    file_put_contents($filename, 'guessExtension');
    expect(File::guessExtension($filename))->toEqual('txt');

    // type 同 filetype
    $filename = $this->testPath . '/type.txt';
    file_put_contents($filename, 'type');
    expect(File::type($filename))->toEqual(filetype($filename));

    // mimeType
    $filename = $this->testPath . '/mimeType.txt';
    file_put_contents($filename, 'mimeType');
    expect(File::mimeType($filename))->toEqual('text/plain');

    // size 文件大小
    $filename = $this->testPath . '/size.txt';
    file_put_contents($filename, 'size');
    expect(File::size($filename))->toEqual(4);

    // lastModified 最后修改时间 同 filemtime
    $filename = $this->testPath . '/filemtime.txt';
    file_put_contents($filename, 'filemtime');
    expect(File::lastModified($filename))->toEqual(filemtime($filename));

    // isDirectory 同 is_dir
    expect(File::isDirectory($this->testPath))->toBeTrue();

    // isReadable 同 is_readable
    expect(File::isWritable($this->testPath))->toBeTrue();

    // isWritable 同 is_writable
    expect(File::isReadable($this->testPath))->toBeTrue();

    // isFile 同 is_file
    expect(File::isFile($this->testPath))->toBeFalse();

    // glob 同 glob
    expect(File::glob($this->testPath . '/*.*'))->toHaveCount(count(glob($this->testPath . '/*.*')));

    // files 获取给定目录下的所有文件
    $count = count(glob($this->testPath . '/*.*'));
    expect(File::files($this->testPath))->toHaveCount($count);
    file_put_contents($this->testPath . '/.gitignore', '');
    expect(File::files($this->testPath, true))->toHaveCount($count + 1);

    // 包含 .gitignore
    // allFiles 获取给定目录下的所有文件，包含子目录
    $count = count(glob($this->testPath . '/*.*'));
    expect(File::allFiles($this->testPath))->toHaveCount($count);
    mkdir($this->testPath . '/allFiles');
    file_put_contents($this->testPath . '/allFiles/newFile.txt', 'allFiles');
    expect(File::allFiles($this->testPath))->toHaveCount($count + 1);

    // 包含子目录下的
    // directories 获取给定目录下的所有子目录
    expect(File::directories($this->testPath))->toHaveCount(1);

    // 上一个测试中创建了1个子目录
    // ensureDirectoryExists 确保目录存在，递归
    $path = $this->testPath . '/ensureDirectoryExists';
    $path2 = $this->testPath . '/ensureDirectoryExists2/depth';
    File::ensureDirectoryExists($path);
    expect(is_dir($path))->toBeTrue();
    File::ensureDirectoryExists($path2);
    // 默认 recursive 为 true
    expect(is_dir($path2))->toBeTrue();

    // makeDirectory 新建目录，非递归
    $path = $this->testPath . '/makeDirectory';
    $path2 = $this->testPath . '/makeDirectory2/depth';
    File::makeDirectory($path);
    expect(is_dir($path))->toBeTrue();
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
    expect(is_dir($path1))->toBeFalse();
    // 移动后 path1 不存在
    expect(is_dir($path2))->toBeTrue();
    expect(file_get_contents($path2 . '/' . $filename))->toEqual($content);

    // 测试 path1 path2 都存在时移动
    mkdir($path1);
    file_put_contents($path1 . '/' . $filename, 'aaa');
    expect(File::moveDirectory($path2, $path1))->toBeFalse();
    // 移动时不覆盖，移动失败
    expect(File::moveDirectory($path2, $path1, true))->toBeTrue();
    // 移动时覆盖，移动成功
    expect(is_dir($path2))->toBeFalse();
    // 移动后 path2 不存在
    expect(file_get_contents($path1 . '/' . $filename))->toEqual($content);

    // copyDirectory 复制目录
    $path1 = $this->testPath . '/copyDirectory';
    $path2 = $this->testPath . '/copyDirectory2';
    $filename = 'copyDirectory.txt';
    $content = 'copyDirectory';
    mkdir($path1);
    file_put_contents($path1 . '/' . $filename, $content);
    expect(File::copyDirectory($path1, $path2))->toBeTrue();
    expect(is_dir($path1))->toBeTrue();
    expect(is_dir($path2))->toBeTrue();
    expect(file_get_contents($path2 . '/' . $filename))->toEqual($content);

    // deleteDirectory 删除目录，递归，保留当前目录
    $path1 = $this->testPath . '/deleteDirectory';
    $filename = 'deleteDirectory.txt';

    // 测试全删
    mkdir($path1);
    file_put_contents($path1 . '/' . $filename, '1');
    expect(File::deleteDirectory($path1))->toBeTrue();
    expect(is_dir($path1))->toBeFalse();

    // 测试保留目录名
    mkdir($path1);
    file_put_contents($path1 . '/' . $filename, '1');
    expect(File::deleteDirectory($path1, true))->toBeTrue();
    expect(is_dir($path1))->toBeTrue();
    expect(is_file($path1 . '/' . $filename))->toBeFalse();
    rmdir($path1);

    // 测试删除非目录
    mkdir($path1);
    file_put_contents($path1 . '/' . $filename, '1');
    expect(File::deleteDirectory($path1 . '/' . $filename))->toBeFalse();

    // deleteDirectories 删除目录下的所有目录，不包含目录下的文件
    $path1 = $this->testPath . '/deleteDirectories';
    $path2 = $this->testPath . '/deleteDirectories/depth';
    $filename = 'deleteDirectories.txt';
    mkdir($path1);
    mkdir($path2);
    file_put_contents($path1 . '/' . $filename, '1');
    expect(File::deleteDirectories($path1))->toBeTrue();
    expect(is_dir($path1))->toBeTrue();
    expect(is_file($path1 . '/' . $filename))->toBeTrue();
    expect(is_dir($path2))->toBeFalse();
    expect(File::deleteDirectories($path1))->toBeFalse();

    // 删除无二级目录时返回 false
    // cleanDirectory 清空目录下的所有文件，但保留目录
    $path1 = $this->testPath . '/cleanDirectory';
    $filename = 'cleanDirectory.txt';
    mkdir($path1);
    file_put_contents($path1 . '/' . $filename, '1');
    expect(File::cleanDirectory($path1))->toBeTrue();
    expect(is_dir($path1))->toBeTrue();
    expect(is_file($path1 . '/' . $filename))->toBeFalse();
});

test('link', function () {
    // link 建立软链
    $linkPath = storage_path() . '/link';
    file_put_contents($this->testPath . '/link.txt', 'link');
    File::link($this->testPath, $linkPath);
    expect(file_exists($linkPath . '/link.txt'))->toBeTrue();
    expect(file_get_contents($linkPath . '/link.txt'))->toEqual('link');
    @rmdir($linkPath);
    // windows
    @unlink($linkPath);

    // linux
    // relativeLink 类 link，使用相对路径
});

test('lines', function () {
    // lines 逐行读取
    $filename = $this->testPath . '/lines.txt';
    try {
        File::lines($filename);
    } catch (Throwable $e) {
        if (checkMethodNotExist($e)) {
            $this->markTestSkipped('>=8.0 才有该方法');
        }
        expect($e)->toBeInstanceOf(FileNotFoundException::class);
    }
    file_put_contents($filename, "111\n222\n333");
    $excepted = ['111', '222', '333'];
    foreach (File::lines($filename) as $index => $line) {
        expect($line)->toEqual($excepted[$index]);
    }
});

test('replace in file', function () {
    // replaceInFile 替换文件中的部分内容
    $filename = $this->testPath . '/replaceInFile.txt';
    file_put_contents($filename, 'hahaha, MeMeMe');
    try {
        File::replaceInFile('xxx', 'yyy', $filename);
    } catch (Throwable $e) {
        if (checkMethodNotExist($e)) {
            $this->markTestSkipped('>=8.0 才有该方法');
        }
    }
    File::replaceInFile('a', 'x', $filename);
    expect(file_get_contents($filename))->toEqual('hxhxhx, MeMeMe');
    File::replaceInFile(['x', 'e'], ['e', 'a'], $filename);
    expect(file_get_contents($filename))->toEqual('hahaha, MaMaMa');
});

function checkMethodNotExist(Throwable $e): bool
{
    // Method Illuminate\Filesystem\Filesystem::lines does not exist.
    return strpos($e->getMessage(), 'Method') !== false
        && strpos($e->getMessage(), 'does not exist') !== false;
}