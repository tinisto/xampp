<?php
// Local database connection that actually works
$connection = new mysqli('127.0.0.1', 'root', 'root', '11klassniki_claude');

// Check the connection
if ($connection->connect_error) {
    // Try without password
    $connection = new mysqli('127.0.0.1', 'root', '', '11klassniki_claude');
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error . "<br>Make sure XAMPP MySQL is running and database '11klassniki_claude' exists.");
    }
}

// Set charset
$connection->set_charset('utf8mb4');

// Make connection available globally
$GLOBALS['connection'] = $connection;
?>