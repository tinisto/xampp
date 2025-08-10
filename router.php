<?php
// Simple router for PHP built-in server
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If requesting a real file that exists, serve it
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Otherwise, handle routing
require_once __DIR__ . '/index_modern.php';
?>