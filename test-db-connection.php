<?php
// Test database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

echo "Testing database connection...<br>";
echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_USER: " . DB_USER . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";

try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($connection->connect_error) {
        echo "Connection failed: " . $connection->connect_error . "<br>";
    } else {
        echo "Successfully connected to database!<br>";
        echo "Server info: " . $connection->server_info . "<br>";
        $connection->close();
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "<br>";
}
?>