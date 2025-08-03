<?php
// Debug VPO regions query
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Debug VPO Regions Query</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// Check regions table structure
echo "<h2>1. Regions Table Structure:</h2>";
$cols = $connection->query("SHOW COLUMNS FROM regions");
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th></tr>";
while ($col = $cols->fetch_assoc()) {
    echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
}
echo "</table>";

// Test the main query
echo "<h2>2. Test Main Query:</h2>";
$sql = "SELECT id, region_name, region_name_en FROM regions WHERE country_id = 1 ORDER BY region_name ASC";
echo "<p>Query: <code>$sql</code></p>";
$result = $connection->query($sql);

if ($result) {
    echo "<p style='color: green;'>✅ Query successful - Found " . $result->num_rows . " regions</p>";
    
    // Show first 3 regions
    echo "<h3>First 3 regions:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>id</th><th>region_name</th><th>region_name_en</th></tr>";
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        if ($count++ >= 3) break;
        echo "<tr><td>{$row['id']}</td><td>{$row['region_name']}</td><td>{$row['region_name_en']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Query failed: " . $connection->error . "</p>";
}

// Check universities table
echo "<h2>3. Universities Table:</h2>";
$uni_cols = $connection->query("SHOW COLUMNS FROM universities");
if ($uni_cols) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th></tr>";
    while ($col = $uni_cols->fetch_assoc()) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
    }
    echo "</table>";
    
    // Count universities
    $count_result = $connection->query("SELECT COUNT(*) as total FROM universities");
    if ($count_result) {
        $total = $count_result->fetch_assoc()['total'];
        echo "<p>Total universities: $total</p>";
    }
    
    // Test count query
    echo "<h3>Test count query for region_id = 1:</h3>";
    $test_count = $connection->query("SELECT COUNT(*) as count FROM universities WHERE region_id = 1");
    if ($test_count) {
        $count = $test_count->fetch_assoc()['count'];
        echo "<p>Universities in region 1: $count</p>";
    } else {
        echo "<p style='color: red;'>Count query error: " . $connection->error . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Could not query universities table</p>";
}

// Test the full logic
echo "<h2>4. Test Full Logic (first region with universities):</h2>";
$sql = "SELECT id, region_name, region_name_en FROM regions WHERE country_id = 1 ORDER BY region_name ASC";
$result = $connection->query($sql);

if ($result) {
    $found = false;
    while ($row = $result->fetch_assoc()) {
        $count_sql = "SELECT COUNT(*) AS count FROM universities WHERE region_id = {$row['id']}";
        $count_result = $connection->query($count_sql);
        
        if ($count_result) {
            $count_row = $count_result->fetch_assoc();
            $institution_count = $count_row['count'];
            
            if ($institution_count > 0) {
                echo "<p>✅ Found: {$row['region_name']} (ID: {$row['id']}) - $institution_count universities</p>";
                $found = true;
                break;
            }
        }
    }
    
    if (!$found) {
        echo "<p style='color: red;'>❌ No regions found with universities!</p>";
    }
}

$connection->close();
?>