<?php

if (!function_exists('storage_path')) {
    /**
     * @param string $path
     * @return string
     */
    function storage_path(string $path = ''): string
    {
        if (!$path) {
            return base_path('storage');
        }
        return path_combine(base_path('storage'), $path);
    }
}
