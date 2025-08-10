<?php
// Local configuration override for XAMPP
// This file takes precedence over loadEnv.php for local development

// Set local environment
$_SERVER['SERVER_NAME'] = 'localhost';

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', '11klassniki_claude');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_URL', 'http://localhost:8000/');
define('SITE_NAME', '11klassniki.ru - Local');
define('IS_LOCAL', true);

// Environment
define('APP_ENV', 'local');
define('APP_DEBUG', true);

// Session
define('SESSION_NAME', '11klassniki_session');

// Email (disabled for local)
define('MAIL_ENABLED', false);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Timezone
date_default_timezone_set('Europe/Moscow');

// Create a simple .env file for compatibility
$envContent = '# Local Environment Configuration
DB_HOST=127.0.0.1
DB_USER=root
DB_PASS=root
DB_NAME=11klassniki_claude
APP_ENV=local
APP_DEBUG=true
SITE_URL=http://localhost:8000/
';

file_put_contents(__DIR__ . '/../.env', $envContent);

// Set $_ENV variables for compatibility
$_ENV['DB_HOST'] = DB_HOST;
$_ENV['DB_USER'] = DB_USER;
$_ENV['DB_PASS'] = DB_PASS;
$_ENV['DB_NAME'] = DB_NAME;
$_ENV['APP_ENV'] = APP_ENV;
$_ENV['APP_DEBUG'] = APP_DEBUG;
$_ENV['SITE_URL'] = SITE_URL;
?>