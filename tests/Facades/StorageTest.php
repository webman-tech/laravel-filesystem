<?php

namespace WebmanTech\LaravelFilesystem\Tests\Facades;

use Illuminate\Contracts\Filesystem\Factory as FactoryContract;
use PHPUnit\Framework\TestCase;
use WebmanTech\LaravelFilesystem\Facades\File;
use WebmanTech\LaravelFilesystem\Facades\Storage;

/**
 * https://laravel.com/docs/10.x/filesystem
 */
class StorageTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        File::cleanDirectory(storage_path());
    }

    public function testInstance()
    {
        $this->assertInstanceOf(FactoryContract::class, Storage::instance());
    }
}
