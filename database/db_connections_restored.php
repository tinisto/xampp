<?php
// Direct database connection for iPage hosting
// This version uses hardcoded values that are known to work

// Establish the database connection
$connection = new mysqli(
    '11klassnikiru67871.ipagemysql.com',
    'admin_claude',
    'W4eZ!#9uwLmrMay',
    '11klassniki_claude'
);

// Check the connection
if ($connection->connect_error) {
    error_log("Database connection failed: " . $connection->connect_error);
    header("Location: /error");
    exit();
}

// Set the charset to utf8mb4
if (!$connection->set_charset('utf8mb4')) {
    error_log("Error setting charset: " . $connection->error);
    // Try utf8 as fallback
    if (!$connection->set_charset('utf8')) {
        error_log("Error setting fallback charset: " . $connection->error);
        header("Location: /error");
        exit();
    }
}

// Optional: Set timezone
$connection->query("SET time_zone = '+03:00'");