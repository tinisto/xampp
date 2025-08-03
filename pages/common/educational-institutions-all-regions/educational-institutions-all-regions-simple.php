<?php
// Simple version without template engine to test if routing works
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple VPO/SPO/Schools Test</h1>";

// Get the type from URL parameter
$type = $_GET['type'] ?? 'schools';
echo "<p>Type parameter: " . htmlspecialchars($type) . "</p>";

// Define table and field names based on type
switch ($type) {
    case 'spo':
        $table = 'spo';
        $pageTitle = 'СПО по регионам';
        break;
    case 'vpo':
        $table = 'vpo';
        $pageTitle = 'ВПО по регионам';
        break;
    default:
        $table = 'schools';
        $pageTitle = 'Школы по регионам';
        break;
}

echo "<h2>" . htmlspecialchars($pageTitle) . "</h2>";

// Try database connection
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($connection->connect_error) {
            echo "<p style='color: red;'>Database connection failed: " . $connection->connect_error . "</p>";
            exit;
        }
        
        $connection->set_charset("utf8mb4");
        echo "<p style='color: green;'>Database connected</p>";
        
        // Query regions
        $sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC LIMIT 5";
        $result = $connection->query($sql);
        
        if ($result && $result->num_rows > 0) {
            echo "<h3>First 5 Regions:</h3><ul>";
            while ($row = $result->fetch_assoc()) {
                // Count institutions in this region
                $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE id_region = {$row['id_region']}";
                $count_result = $connection->query($count_sql);
                $count = 0;
                if ($count_result) {
                    $count_row = $count_result->fetch_assoc();
                    $count = $count_row['count'];
                }
                
                echo "<li>" . htmlspecialchars($row['region_name']) . " (" . $count . " institutions)</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>No regions found</p>";
        }
        
        $connection->close();
    } else {
        echo "<p style='color: red;'>Database constants not defined</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<p><strong>If you see regions listed above, the routing and database are working!</strong></p>";
?>