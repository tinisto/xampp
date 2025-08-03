<?php
// Minimal environment setup without Composer dependencies

// Define database constants directly
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'u2709849_default');
    define('DB_PASS', 'JWBr0F_0');
    define('DB_NAME', 'u2709849_default');
}

// Set environment variables
$_ENV['APP_ENV'] = 'production';
$_ENV['DB_HOST'] = DB_HOST;
$_ENV['DB_USER'] = DB_USER;
$_ENV['DB_PASS'] = DB_PASS;
$_ENV['DB_NAME'] = DB_NAME;
?>