<?php

namespace Kriss\WebmanFilesystem\Overwrite\Traits;

use Illuminate\Support\Str;
use Webman\File;
use Webman\Http\UploadFile;

/**
 * 替换 Illuminate\Http 相关的
 */
trait ChangeHttpUse
{
    /**
     * @inheritDoc
     * @return \support\Response
     */
    public function response($path, $name = null, array $headers = [], $disposition = 'inline')
    {
        $response = response()->withHeaders($headers);
        if ($name) {
            return $response->download($path, $name);
        }
        return $response->withFile($path);
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

        if ($contents instanceof UploadFile) {
            return $this->putFile($path, $contents, $options);
        }

        return parent::put($path, $contents, $options);
    }

    /**
     * @inheritDoc
     * @param UploadFile|File|string $file
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