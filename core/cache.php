<?php

header_remove('Content-Security-Policy');

header("Content-Security-Policy: default-src *;");
// header("Content-Security-Policy: connect-src 'self' *;");

$toCacheResponse = to_bool(
    request_header_get('X-To-Cache') ??
    request_header_get('To-Cache') ??
    request_header_get('X-To-Cache-Response') ??
    request_header_get('To-Cache-Response')
);

$_publicCache = true;

if ($toCacheResponse && $_publicCache) {
    header("Cache-Control: public"); // Indicates that the response can be cached
}

if ($toCacheResponse && !$_publicCache) {
    $threeHoursInSeconds = 3 * 60; // Calculate 3 minutes in seconds

    header("Cache-Control: public, max-age={$threeHoursInSeconds}"); // Public caching, max-age in seconds
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + $threeHoursInSeconds) . " GMT"); // Expires timestamp in GMT
    header("Pragma: public"); // For compatibility with older browsers
    // header("Last-Modified: " . gmdate("D, d M Y H:i:s", filemtime(__FILE__)) . " GMT"); // Last-modified timestamp (optional, good practice)
    header("ETag: " . md5_file(__FILE__)); // ETag (optional, good practice)
}

if (!is_to_cache()) {
    // Here I send clear cache instructions if it exists
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
}
