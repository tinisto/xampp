<?php
// Debug SPO/VPO field names
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug SPO/VPO Field Names</h1>";

// Database connection
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }
        
        $connection->set_charset("utf8mb4");
        echo "<p style='color: green;'>Database connected</p>";
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Check SPO table structure
echo "<h2>SPO Table Structure:</h2>";
$result = $connection->query("SHOW COLUMNS FROM spo");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    echo "</table>";
}

// Check sample SPO data
echo "<h2>Sample SPO Data (first 3 records):</h2>";
$result = $connection->query("SELECT * FROM spo LIMIT 3");
if ($result) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
}

// Check VPO table structure
echo "<h2>VPO Table Structure:</h2>";
$result = $connection->query("SHOW COLUMNS FROM vpo");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    echo "</table>";
}

// Check sample VPO data
echo "<h2>Sample VPO Data (first 3 records):</h2>";
$result = $connection->query("SELECT * FROM vpo LIMIT 3");
if ($result) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
}

// Check schools table structure
echo "<h2>Schools Table Structure:</h2>";
$result = $connection->query("SHOW COLUMNS FROM schools");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    echo "</table>";
}

// Check sample schools data
echo "<h2>Sample Schools Data (first 3 records):</h2>";
$result = $connection->query("SELECT * FROM schools LIMIT 3");
if ($result) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
}

$connection->close();
?>