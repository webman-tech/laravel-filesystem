<?php

namespace WebmanTech\LaravelFilesystem\Extend\FlysystemV1;

use AlphaSnow\AliyunOss\Adapter;
use AlphaSnow\AliyunOss\Config;
use AlphaSnow\Flysystem\AliyunOss\Plugins\AppendContent;
use AlphaSnow\Flysystem\AliyunOss\Plugins\AppendFile;
use AlphaSnow\Flysystem\AliyunOss\Plugins\AppendObject;
use League\Flysystem\Filesystem;
use League\Flysystem\Config as FlysystemConfig;
use OSS\OssClient;

/**
 * @link https://github.com/alphasnow/aliyun-oss-laravel/blob/3.x/src/ServiceProvider.php
 */
class OssAlphaSnowExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createFilesystem($config): Filesystem
    {
        $ossConfig = new Config($config);

        $ossClient = new OssClient($ossConfig->get('access_id'), $ossConfig->get('access_key'), $ossConfig->getOssEndpoint(), $ossConfig->isCName(), $ossConfig->get('security_token'), $ossConfig->get('request_proxy'));
        $ossConfig->has('use_ssl') && $ossClient->setUseSSL($ossConfig->get('use_ssl'));
        $ossConfig->has('max_retries') && $ossClient->setMaxTries($ossConfig->get('max_retries'));
        $ossConfig->has('enable_sts_in_url') && $ossClient->setSignStsInUrl($ossConfig->get('enable_sts_in_url'));
        $ossConfig->has('timeout') && $ossClient->setTimeout($ossConfig->get('timeout'));
        $ossConfig->has('connect_timeout') && $ossClient->setConnectTimeout($ossConfig->get('connect_timeout'));

        $adapter = new Adapter($ossClient, $ossConfig->get('bucket'), $ossConfig->get('prefix', ''), $ossConfig->get('options', []));
        $adapter->setOssConfig($ossConfig);

        $filesystem = new Filesystem($adapter, new FlysystemConfig(['disable_asserts' => true]));
        $filesystem->addPlugin(new AppendContent());
        $filesystem->addPlugin(new AppendObject());
        $filesystem->addPlugin(new AppendFile());

        return $filesystem;
    }
}
