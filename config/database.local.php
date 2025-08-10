<?php
// Local database configuration
define('DB_HOST', '127.0.0.1'); // Use IP instead of localhost for XAMPP
define('DB_USER', 'root');
define('DB_PASS', 'root'); // XAMPP password found in phpMyAdmin config
define('DB_NAME', '11klassniki_claude');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Environment flag
define('IS_LOCAL', true);

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Timezone
date_default_timezone_set('Europe/Moscow');
?>