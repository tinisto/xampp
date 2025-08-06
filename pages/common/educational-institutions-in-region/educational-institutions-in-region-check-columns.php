<?php
// Check actual column names in database
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>DATABASE STRUCTURE CHECK\n";
echo "========================\n\n";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Check regions table structure
echo "REGIONS TABLE COLUMNS:\n";
$query = "SHOW COLUMNS FROM regions";
$result = $connection->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "ERROR: " . $connection->error . "\n";
}

echo "\n";

// Check VPO table structure
echo "VPO TABLE COLUMNS:\n";
$query = "SHOW COLUMNS FROM vpo";
$result = $connection->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "ERROR: " . $connection->error . "\n";
}

echo "\n";

// Try a simple query on regions
echo "TEST QUERY ON REGIONS:\n";
$query = "SELECT * FROM regions WHERE region_name_en = 'amurskaya-oblast' LIMIT 1";
$result = $connection->query($query);
if ($result) {
    $row = $result->fetch_assoc();
    if ($row) {
        echo "Found region:\n";
        foreach ($row as $key => $value) {
            echo "  $key: $value\n";
        }
    } else {
        echo "No region found with region_name_en = 'amurskaya-oblast'\n";
    }
} else {
    echo "ERROR: " . $connection->error . "\n";
}

echo "</pre>";
?>