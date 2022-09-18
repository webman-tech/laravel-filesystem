<?php

namespace WebmanTech\LaravelFilesystem\Traits;

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
}