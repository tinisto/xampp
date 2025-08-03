<?php
// Patched loadEnv.php that works without Composer

// Try to use the simple loader first
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv_simple.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv_simple.php';
    return;
}

// If Composer autoloader exists, use it
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    use Dotenv\Dotenv;
    
    $dotenvPath = $_SERVER['DOCUMENT_ROOT'];
    if (file_exists($dotenvPath . '/.env')) {
        $dotenv = Dotenv::createImmutable($dotenvPath);
        try {
            $dotenv->load();
        } catch (Exception $e) {
            // Fall back to simple loader
            require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv_simple.php';
        }
    }
} else {
    // No Composer, use simple loader
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv_simple.php';
}

// Ensure constants are defined
if (!defined('DB_HOST') && isset($_ENV['DB_HOST'])) {
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_PASS', $_ENV['DB_PASS']);
    define('DB_NAME', $_ENV['DB_NAME']);
}
?>