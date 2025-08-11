<?php
/**
 * PHPUnit Bootstrap for 11klassniki
 * Sets up test environment and dependencies
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Set testing environment
define('TESTING', true);
define('DEBUG', true);

// Set up test environment variables
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/test';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'PHPUnit Test';

// Start session for testing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create test directories if they don't exist
$testDirs = [
    __DIR__ . '/coverage',
    __DIR__ . '/results',
    __DIR__ . '/../logs',
    __DIR__ . '/../cache',
    __DIR__ . '/../cache/queries'
];

foreach ($testDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Load testing utilities
require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/DatabaseTestCase.php';

echo "PHPUnit Bootstrap: Test environment initialized\n";