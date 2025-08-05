<?php
// Check the actual regions table structure
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Check Regions Table Structure</h1>";

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (!isset($connection) || !$connection) {
        echo "<p>❌ Database connection not available</p>";
        exit;
    }
    
    echo "<p>✅ Database connected</p>";
    
    // Check regions table structure
    echo "<h2>📊 Regions Table Structure</h2>";
    $result = $connection->query("SHOW COLUMNS FROM regions");
    if ($result) {
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Error checking regions table: " . $connection->error . "</p>";
    }
    
    // Check schools table for region reference
    echo "<h2>📊 Schools Table Region Fields</h2>";
    $result = $connection->query("SHOW COLUMNS FROM schools LIKE '%region%'");
    if ($result) {
        echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
        }
        echo "</table>";
    }
    
    // Check VPO table for region reference
    echo "<h2>📊 VPO Table Region Fields</h2>";
    $result = $connection->query("SHOW COLUMNS FROM vpo LIKE '%region%'");
    if ($result) {
        echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
        }
        echo "</table>";
    }
    
    // Check SPO table for region reference
    echo "<h2>📊 SPO Table Region Fields</h2>";
    $result = $connection->query("SHOW COLUMNS FROM spo LIKE '%region%'");
    if ($result) {
        echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
        }
        echo "</table>";
    }
    
    // Test query
    echo "<h2>🧪 Test Query</h2>";
    $test_query = "SELECT * FROM regions LIMIT 1";
    $test_result = $connection->query($test_query);
    if ($test_result) {
        $sample = $test_result->fetch_assoc();
        echo "<p>Sample region record:</p>";
        echo "<pre>" . print_r($sample, true) . "</pre>";
    } else {
        echo "<p>❌ Test query failed: " . $connection->error . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>