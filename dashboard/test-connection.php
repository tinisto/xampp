<?php
session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

// Try to include files with error handling
$config_path = dirname(__DIR__) . '/config/loadEnv.php';
$db_path = dirname(__DIR__) . '/database/db_connections.php';

echo "Config path: " . $config_path . "<br>";
echo "DB path: " . $db_path . "<br>";

if (!file_exists($config_path)) {
    die("Error: Config file not found at: " . $config_path);
}

if (!file_exists($db_path)) {
    die("Error: Database file not found at: " . $db_path);
}

require_once $config_path;
require_once $db_path;

// Test database connection
if (!isset($connection)) {
    die("Error: Database connection not established");
}

echo "Database connection successful!";
?>