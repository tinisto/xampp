<?php
// Minimal test version
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Basic database connection
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Get parameters
$region_name_en = $_GET['region_name_en'] ?? '';
$type = $_GET['type'] ?? '';

// Basic output
echo "<!DOCTYPE html><html><head><title>Test</title></head><body>";
echo "<h1>Debug Information</h1>";
echo "<p>Region: " . htmlspecialchars($region_name_en) . "</p>";
echo "<p>Type: " . htmlspecialchars($type) . "</p>";

// Test regions table
try {
    $query = "SELECT * FROM regions WHERE region_name_en = ? LIMIT 1";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $region_name_en);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        echo "<p>Region found: " . $row['region_name'] . " (ID: " . $row['id_region'] . ")</p>";
    } else {
        echo "<p>Region not found</p>";
    }
    $stmt->close();
} catch (Exception $e) {
    echo "<p>Region query error: " . $e->getMessage() . "</p>";
}

// Test table structure
if ($type && in_array($type, ['vpo', 'spo', 'schools'])) {
    try {
        $query = "SHOW COLUMNS FROM $type";
        $result = $connection->query($query);
        echo "<h2>Columns in $type table:</h2><ul>";
        while ($col = $result->fetch_assoc()) {
            echo "<li>" . $col['Field'] . " (" . $col['Type'] . ")</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p>Table structure error: " . $e->getMessage() . "</p>";
    }
}

echo "</body></html>";
?>