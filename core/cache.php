<?php

$_publicCache = true;

if ($_publicCache) {
    header("Cache-Control: public"); // Indicates that the response can be cached
}

if (!$_publicCache) {
    $threeHoursInSeconds = 3 * 60 * 60; // Calculate 3 hours in seconds

    header("Cache-Control: public, max-age={$threeHoursInSeconds}"); // Public caching, max-age in seconds
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + $threeHoursInSeconds) . " GMT"); // Expires timestamp in GMT
    header("Pragma: public"); // For compatibility with older browsers
    // header("Last-Modified: " . gmdate("D, d M Y H:i:s", filemtime(__FILE__)) . " GMT"); // Last-modified timestamp (optional, good practice)
    header("ETag: " . md5_file(__FILE__)); // ETag (optional, good practice)
}