<?php

if (!function_exists('storage_path')) {
    /**
     * @return string
     */
    function storage_path(): string
    {
        return BASE_PATH . DIRECTORY_SEPARATOR . 'storage';
    }
}