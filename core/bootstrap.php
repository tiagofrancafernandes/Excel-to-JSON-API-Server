<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/..'));
}

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../vendor/symfony/var-dumper/Resources/functions/dump.php';

require_once __DIR__ . '/cors.php';

require_once __DIR__ . '/cache.php';
