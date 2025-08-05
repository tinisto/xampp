<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Simple Region Debug</h1>";

// Test basic connection
echo "Database connection: " . ($connection ? "✅ OK" : "❌ Failed") . "<br>";

// Test regions table
echo "<h2>Regions table test:</h2>";
$result = $connection->query("SELECT * FROM regions WHERE region_name_en = 'amurskaya-oblast'");
if ($result) {
    echo "Query successful. Rows found: " . $result->num_rows . "<br>";
    if ($row = $result->fetch_assoc()) {
        echo "Region ID: " . $row['region_id'] . "<br>";
        echo "Region Name: " . $row['region_name'] . "<br>";
        
        $region_id = $row['region_id'];
        
        // Test VPO query
        echo "<h2>VPO test:</h2>";
        $vpo_result = $connection->query("SELECT COUNT(*) as count FROM vpo WHERE region_id = $region_id");
        if ($vpo_result) {
            $vpo_count = $vpo_result->fetch_assoc()['count'];
            echo "VPO count in region: $vpo_count<br>";
        } else {
            echo "VPO query failed: " . $connection->error . "<br>";
        }
        
        // Test if region_id field exists in vpo
        $check_field = $connection->query("SHOW COLUMNS FROM vpo LIKE 'region_id'");
        echo "VPO has region_id field: " . ($check_field->num_rows > 0 ? "✅ YES" : "❌ NO") . "<br>";
    }
} else {
    echo "Query failed: " . $connection->error . "<br>";
}

// Test if the educational-institutions-in-region.php file has syntax errors
echo "<h2>Checking region page file:</h2>";
$region_file = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-in-region/educational-institutions-in-region.php';
if (file_exists($region_file)) {
    echo "Region file exists: ✅ YES<br>";
    
    // Check for syntax errors
    $output = shell_exec("php -l $region_file 2>&1");
    if (strpos($output, 'No syntax errors') !== false) {
        echo "Syntax check: ✅ OK<br>";
    } else {
        echo "Syntax errors found:<br><pre>$output</pre>";
    }
} else {
    echo "Region file exists: ❌ NO<br>";
}
?>