<?php

declare(strict_types=1);
ob_start();

use App\Excel\Reader;

require_once __DIR__ . '/../core/bootstrap.php';

if (filter_var(request_any_get('tracy', false), FILTER_VALIDATE_BOOL)) {
    require_once __DIR__ . '/tracy.php';
}

Reader::response();
ob_end_flush();
