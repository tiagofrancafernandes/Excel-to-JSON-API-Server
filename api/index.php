<?php

declare(strict_types=1);
ob_start();

use App\Excel\Reader;

require_once __DIR__ . '/../core/bootstrap.php';

if (filter_var(request_any_get('ui', false), FILTER_VALIDATE_BOOL) || filter_var(request_any_get('parser', false), FILTER_VALIDATE_BOOL)) {
    $content = file_get_contents(__DIR__ . '/js-parser.html');

    echo $content;
    die;
}

if (get_env('APP_ENV') !== 'production' && filter_var(request_any_get('tracy', false), FILTER_VALIDATE_BOOL)) {
    require __DIR__ . '/tracy.php';
}

Reader::response();
ob_end_flush();
