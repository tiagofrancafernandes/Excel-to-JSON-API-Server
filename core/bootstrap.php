<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/..'));
}

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../vendor/symfony/var-dumper/Resources/functions/dump.php';

require_once __DIR__ . '/cors.php';

$toCache = !filter_var(request_any_get('no-cache', false), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

if ($toCache) {
    require_once __DIR__ . '/cache.php';
}

