<?php

namespace WebmanTech\LaravelFilesystem\Mock;

use Illuminate\Filesystem\FilesystemAdapter as LaravelFilesystemAdapter;
use WebmanTech\CommonUtils\Response;

/**
 * @internal
 */
final class FilesystemAdapter extends LaravelFilesystemAdapter
{
    public static function wrapper(LaravelFilesystemAdapter $filesystemAdapter): self
    {
        return new self($filesystemAdapter->getDriver(), $filesystemAdapter->getAdapter(), $filesystemAdapter->getConfig());
    }

    /**
     * @inheritDoc
     * @return \Webman\Http\Response|\Symfony\Component\HttpFoundation\Response|mixed 宿主原始响应对象
     */
    public function response($path, $name = null, array $headers = [], $disposition = 'inline')
    {
        $response = Response::make();
        $filename = $name ?? basename($path);
        $response->withHeaders($headers + [
                'Content-Type' => $this->mimeType($path),
                'Content-Length' => $this->size($path),
                'Content-Disposition' => "{$disposition}; filename=\"$filename\"",
            ]);
        ob_start();
        $stream = $this->readStream($path);
        fpassthru($stream);
        fclose($stream);
        $content = ob_get_clean();
        return $response->withBody($content)->getRaw();
    }
}
