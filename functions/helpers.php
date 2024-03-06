<?php

if (!function_exists('get_env')) {
    /**
     * get_env function
     *
     * Gets the value of an environment variable, or all
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    function get_env(?string $key = null, mixed $default = null): mixed
    {
        return App\Helpers\Env::init()->getEnv($key, $default);
    }
}

if (!function_exists('base_path')) {
    /**
     * function base_path
     *
     * @param string $path = ''
     *
     * @return string
     */
    function base_path(string $path = ''): ?string
    {
        if (!defined('BASE_PATH')) {
            return null;
        }

        return BASE_PATH . '/' . ltrim($path, '/');
    }
}
