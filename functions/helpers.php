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

if (!function_exists('temp_path')) {
    /**
     * function temp_path
     *
     * @param string $path = ''
     *
     * @return string
     */
    function temp_path(string $path = ''): ?string
    {
        $tempDir = sys_get_temp_dir();

        if (!$tempDir || !$path) {
            return $tempDir ?: null;
        }

        return $tempDir . '/' . ltrim($path, '/');
    }
}

if (!function_exists('request_query_get')) {
    /**
     * function request_query_get
     *
     * @param ?string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    function request_query_get(?string $key, mixed $defaultValue = null): mixed
    {
        return $_GET[$key] ?? $defaultValue;
    }
}

if (!function_exists('request_post_get')) {
    /**
     * function request_post_get
     *
     * @param ?string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    function request_post_get(?string $key, mixed $defaultValue = null): mixed
    {
        return $_POST[$key] ?? $defaultValue;
    }
}

if (!function_exists('request_any_get')) {
    /**
     * function request_any_get
     *
     * @param ?string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    function request_any_get(?string $key, mixed $defaultValue = null): mixed
    {
        return $_REQUEST[$key] ?? $_GET[$key] ?? $_POST[$key] ?? $defaultValue;
    }
}

if (!function_exists('request_cookie_get')) {
    /**
     * function request_cookie_get
     *
     * @param ?string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    function request_cookie_get(?string $key, mixed $defaultValue = null): mixed
    {
        return $_COOKIE[$key] ?? $defaultValue;
    }
}

if (!function_exists('request_server_get')) {
    /**
     * function request_server_get
     *
     * @param ?string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    function request_server_get(?string $key, mixed $defaultValue = null): mixed
    {
        return $_SERVER[$key] ?? $defaultValue;
    }
}

if (!function_exists('request_path')) {
    /**
     * function request_path
     *
     * @param string $defaultValue
     *
     * @return mixed
     */
    function request_path(string $defaultValue = '/'): string
    {
        return $_SERVER['PATH_INFO'] ?? $defaultValue;
    }
}

if (!function_exists('request_header_get')) {
    /**
     * function request_header_get
     *
     * @param ?string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    function request_header_get(?string $key, mixed $defaultValue = null): mixed
    {
        if (!$key) {
            return $defaultValue;
        }

        $key = str_replace(['-', '_'], '_', $key);

        $key = strtoupper("HTTP_{$key}");
        $forwardedkey = strtoupper("HTTP_X_FORWARDED_{$key}");

        return $_SERVER[$key] ?? $_SERVER[$forwardedkey] ?? $defaultValue;
    }
}

if (!function_exists('app_abort')) {
    function app_abort(int $code, string $message = ''): void
    {
        if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) && !headers_sent()) {
            $code = $code > 100 && $code <= 500 ? $code : 500;

            $message = $message ?: match ($code) {
                500 => 'Server error',
                404 => 'Not found',
                '' => '',
            };
            header("HTTP/1.1 {$code} {$message}");
        }

        exit((int) $code);
    }
}

if (!function_exists('response_as_json')) {
    /**
     * function response_as_json
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     *
     * @return void
     */
    function response_as_json(
        mixed $data,
        int $statusCode = 200,
        array $headers = [],
    ): void {
        foreach ($headers as $key => $value) {
            if (!is_string($key) || !is_string($value) || !trim($key) || !trim($value)) {
                continue;
            }

            header("{$key}: {$value}", true);
        }

        header('Content-Type: application/json', true);
        header('App-Creator: TiagoFranca.com', true);

        $statusCode = $statusCode > 100 && $statusCode <= 500 ? $statusCode : 500;
        http_response_code($statusCode);

        die(json_encode($data, 64));
    }
}

if (!function_exists('on_cli')) {
    /**
     * function on_cli
     *
     * @param
     * @return bool
     */
    function on_cli(): bool
    {
        return \PHP_SAPI === 'cli';
    }
}

if (!function_exists('request_input_bool')) {
    /**
     * function request_input_bool
     *
     * @param ?string $key
     * @param ?bool $defaultValue
     *
     * @return mixed
     */
    function request_input_bool(?string $key, ?bool $defaultValue = null): mixed
    {
        $value = request_any_get($key, null);

        if ($value === '') {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $defaultValue;
    }
}

if (!function_exists('to_bool')) {
    /**
     * function to_bool
     *
     * @param mixed $value
     * @param ?bool $defaultValue
     *
     * @return mixed
     */
    function to_bool(mixed $value, ?bool $defaultValue = null): mixed
    {
        return boolval(filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $defaultValue);
    }
}

if (!function_exists('is_to_cache')) {
    /**
     * function is_to_cache
     *
     * @return bool
     */
    function is_to_cache(): bool
    {
        $noCacheValue = request_header_get('no-cache', request_any_get('no-cache'));
        $noCacheValue = $noCacheValue === "" ? true : to_bool($noCacheValue);

        if ($noCacheValue) {
            return false;
        }

        $toCacheValue = request_header_get('to-cache', request_any_get('to-cache')) ?? true;
        $toCacheValue = $toCacheValue === "" ? true : to_bool($toCacheValue);

        return to_bool($toCacheValue);
    }
}

if (!function_exists('die_as_json')) {
    /**
     * function die_as_json
     *
     * @param mixed ...$data
     *
     * @return void
     */
    function die_as_json(
        mixed ...$data,
    ): void {
        response_as_json($data, 500);

        die;
    }
}
