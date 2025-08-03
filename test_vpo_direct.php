<?php
// Direct test of VPO regions without template
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Test VPO Direct</title></head><body>";
echo "<h1>Direct VPO Regions Test</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// Settings for VPO
$table = 'universities';
$regionColumn = 'region_id';
$linkPrefix = '/vpo-in-region';

// Get regions
$sql = "SELECT id, region_name, region_name_en FROM regions WHERE country_id = 1 ORDER BY region_name ASC";
$result = $connection->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Found " . $result->num_rows . " regions</p>";
    echo "<h2>Regions with Universities:</h2>";
    echo "<ul>";
    
    $displayed_count = 0;
    while ($row = $result->fetch_assoc()) {
        // Count institutions in this region
        $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE $regionColumn = {$row['id']}";
        $count_result = $connection->query($count_sql);
        
        if ($count_result) {
            $count_row = $count_result->fetch_assoc();
            $institution_count = $count_row['count'];
            
            if ($institution_count > 0) {
                $displayed_count++;
                echo "<li>";
                echo "<a href='$linkPrefix/{$row['region_name_en']}'>";
                echo htmlspecialchars($row['region_name']);
                echo "</a> - $institution_count universities";
                echo "</li>";
                
                if ($displayed_count >= 10) {
                    echo "<li>...and more</li>";
                    break;
                }
            }
        } else {
            echo "<li style='color: red;'>Error counting for region {$row['id']}: " . $connection->error . "</li>";
        }
    }
    echo "</ul>";
    
    if ($displayed_count == 0) {
        echo "<p>No regions found with universities.</p>";
    } else {
        echo "<p>Total regions with universities shown: $displayed_count</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Query failed: " . $connection->error . "</p>";
}

echo "</body></html>";
$connection->close();
?>