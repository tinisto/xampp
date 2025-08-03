<?php
// Debug version to test data
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$type = $_GET['type'] ?? 'schools';
$table = '';
$countField = '';

switch ($type) {
    case 'schools':
        $table = 'schools';
        $countField = 'school_count';
        break;
    case 'spo':
        $table = 'spo';
        $countField = 'spo_count';
        break;
    case 'vpo':
        $table = 'vpo';
        $countField = 'vpo_count';
        break;
}

echo "<h1>Debug: $type</h1>";
echo "<p>Table: $table</p>";
echo "<p>Count Field: $countField</p>";

// Test query
$sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 LIMIT 5";
$result = $connection->query($sql);

if ($result) {
    echo "<p>✓ Regions query successful. Found " . $result->num_rows . " regions</p>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<p>Region: " . $row['region_name'] . " (ID: " . $row['id_region'] . ")</p>";
        
        // Test count query
        $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE id_region = " . $row['id_region'];
        $count_result = $connection->query($count_sql);
        
        if ($count_result) {
            $count = $count_result->fetch_assoc()['count'];
            echo "<p>&nbsp;&nbsp;→ Count in $table: $count</p>";
        } else {
            echo "<p>&nbsp;&nbsp;✗ Count query failed: " . $connection->error . "</p>";
        }
    }
} else {
    echo "<p>✗ Regions query failed: " . $connection->error . "</p>";
}

// Check if tables exist
$tables = ['schools', 'vpo', 'spo'];
foreach ($tables as $t) {
    $check = $connection->query("SHOW TABLES LIKE '$t'");
    echo "<p>Table '$t' exists: " . ($check->num_rows > 0 ? 'YES' : 'NO') . "</p>";
}
?>