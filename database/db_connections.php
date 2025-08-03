<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

// TEMPORARY FIX: Force new database connection while PHP cache is clearing
// This can be removed once PHP properly reads the new .env values
$force_new_db = true; // Set to false to revert to normal behavior

if ($force_new_db) {
    // Force connection to new database
    $connection = new mysqli(
        '11klassnikiru67871.ipagemysql.com',
        'admin_claude',
        'W4eZ!#9uwLmrMay',
        '11klassniki_claude'
    );
} else {
    // Check if the constants are defined
    if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASS') || !defined('DB_NAME')) {
        header("Location: /error");
        exit();
    }
    
    // Establish the database connection using .env values
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
}

// Check the connection
if ($connection->connect_error) {
    header("Location: /error");
    exit();
}

// Check if the server supports utf8mb4
$result = $connection->query("SHOW CHARACTER SET LIKE 'utf8mb4';");
if (!$result || $result->num_rows == 0) {
    header("Location: /error");
    exit();
}

// Set the charset
if (!$connection->set_charset('utf8mb4')) {
    header("Location: /error");
    exit();
}
