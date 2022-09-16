<?php

namespace WebmanTech\LaravelFilesystem\Traits;

use Illuminate\Support\Str;
use WebmanTech\LaravelFilesystem\Http\UploadedFile;
use Webman\File;
use Webman\Http\UploadFile;

/**
 * 替换 Illuminate\Http 相关的
 */
trait ChangeHttpUse
{
    /**
     * @inheritDoc
     * @return \Webman\Http\Response
     */
    public function download($path, $name = null, array $headers = [])
    {
        return $this->response($path, $name, $headers, 'attachment');
    }

    /**
     * @inheritDoc
     * @return \Webman\Http\Response
     */
    public function response($path, $name = null, array $headers = [], $disposition = 'inline')
    {
        $response = response();
        $filename = $name ?? basename($path);
        foreach ($headers + [
            'Content-Type' => $this->mimeType($path),
            'Content-Length' => $this->size($path),
            'Content-Disposition' => "{$disposition}; filename=\"$filename\"",
        ] as $key => $value) {
            $response->withHeader($key, $value);
        }
        ob_start();
        $stream = $this->readStream($path);
        fpassthru($stream);
        fclose($stream);
        $content = ob_get_clean();
        return $response->withBody($content);
    }

    /**
     * @inheritDoc
     * @param UploadFile|File|resource|string $contents
     */
    public function put($path, $contents, $options = [])
    {
        $options = is_string($options)
            ? ['visibility' => $options]
            : (array) $options;

        if ($contents instanceof File) {
            return $this->putFile($path, $contents, $options);
        }

        return parent::put($path, $contents, $options);
    }

    /**
     * @inheritDoc
     * @param UploadedFile|UploadFile|File|string $file
     */
    public function putFile($path, $file, $options = [])
    {
        $file = is_string($file) ? new File($file) : $file;

        $hashName = Str::random(40);
        if ($extension = $file->getExtension()) {
            $hashName .= '.' . $extension;
        }

        return $this->putFileAs($path, $file, $hashName, $options);
    }
}