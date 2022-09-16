<?php

namespace WebmanTech\LaravelFilesystem\Http;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;
use WebmanTech\LaravelFilesystem\Facades\Storage;
use Webman\Http\UploadFile;

/**
 * @see https://github.com/laravel/framework/blob/9.x/src/Illuminate/Http/UploadedFile.php
 * 修改为 webman 支持
 */
class UploadedFile extends UploadFile
{
    use FileHelpers, Macroable, SymfonyUploadedFileSupport;

    public static function wrapper(UploadFile $uploadFile): self
    {
        return new self(
            $uploadFile->getRealPath(),
            $uploadFile->getUploadName(),
            $uploadFile->getUploadMineType(),
            $uploadFile->getUploadErrorCode()
        );
    }

    /**
     * Begin creating a new file fake.
     */
    public static function fake()
    {
        throw new \InvalidArgumentException('Not Support');
    }

    /**
     * Store the uploaded file on a filesystem disk.
     *
     * @param string $path
     * @param array|string $options
     * @return string|false
     */
    public function store($path, $options = [])
    {
        return $this->storeAs($path, $this->hashName(), $this->parseOptions($options));
    }

    /**
     * Store the uploaded file on a filesystem disk with public visibility.
     *
     * @param string $path
     * @param array|string $options
     * @return string|false
     */
    public function storePublicly($path, $options = [])
    {
        $options = $this->parseOptions($options);

        $options['visibility'] = 'public';

        return $this->storeAs($path, $this->hashName(), $options);
    }

    /**
     * Store the uploaded file on a filesystem disk with public visibility.
     *
     * @param string $path
     * @param string $name
     * @param array|string $options
     * @return string|false
     */
    public function storePubliclyAs($path, $name, $options = [])
    {
        $options = $this->parseOptions($options);

        $options['visibility'] = 'public';

        return $this->storeAs($path, $name, $options);
    }

    /**
     * Store the uploaded file on a filesystem disk.
     *
     * @param string $path
     * @param string $name
     * @param array|string $options
     * @return string|false
     */
    public function storeAs($path, $name, $options = [])
    {
        $options = $this->parseOptions($options);

        $disk = Arr::pull($options, 'disk');

        return Storage::disk($disk)->putFileAs(
            $path, $this, $name, $options
        );
    }

    /**
     * Get the contents of the uploaded file.
     *
     * @return false|string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get()
    {
        if (!$this->isValid()) {
            throw new FileNotFoundException("File does not exist at path {$this->getPathname()}.");
        }

        return file_get_contents($this->getPathname());
    }

    /**
     * Get the file's extension supplied by the client.
     *
     * @return string
     */
    public function clientExtension()
    {
        return $this->guessClientExtension();
    }

    /**
     * Create a new file instance from a base instance.
     *
     * @param $file
     * @param bool $test
     * @return static
     */
    public static function createFromBase($file, $test = false)
    {
        throw new \InvalidArgumentException('Not Support');
    }

    /**
     * Parse and format the given options.
     *
     * @param array|string $options
     * @return array
     */
    protected function parseOptions($options)
    {
        if (is_string($options)) {
            $options = ['disk' => $options];
        }

        return $options;
    }
}