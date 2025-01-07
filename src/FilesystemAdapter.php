<?php

namespace WebmanTech\LaravelFilesystem;

use Illuminate\Filesystem\FilesystemAdapter as LaravelFilesystemAdapter;

/**
 * @internal
 */
final class FilesystemAdapter extends LaravelFilesystemAdapter
{
    public static function wrapper(LaravelFilesystemAdapter $filesystemAdapter)
    {
        return new self($filesystemAdapter->getDriver(), $filesystemAdapter->getAdapter(), $filesystemAdapter->getConfig());
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
}
