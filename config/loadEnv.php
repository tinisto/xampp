<?php
// loadEnv.php

// Ensure the Composer autoloader is included
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load the environment variables from .env file
$dotenvPath = $_SERVER['DOCUMENT_ROOT'];
if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv::createImmutable($dotenvPath);
    try {
        $dotenv->load();
    } catch (Exception $e) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }
} else {
    header("Location: /error");
    exit();
}

// Check if APP_ENV is set to 'under_construction'
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'under_construction') {
    // If under construction, we don't need to check for database variables
    return;
}

// Check if environment variables are loaded
if (!isset($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME'])) {
    header("Location: /error");
    exit();
}

// Define the constants using the loaded environment variables
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);
