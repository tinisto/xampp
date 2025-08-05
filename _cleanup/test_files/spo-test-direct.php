<?php
// Direct SPO test without includes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Direct database connection
$connection = new mysqli(
    '11klassnikiru67871.ipagemysql.com',
    'admin_claude',
    'W4eZ!#9uwLmrMay',
    '11klassniki_claude'
);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$connection->set_charset("utf8mb4");

// SPO settings
$table = 'colleges';
$linkPrefix = '/spo-in-region';
$pageTitle = 'СПО по регионам';
$regionColumn = 'region_id';

echo "<!DOCTYPE html>";
echo "<html><head><title>$pageTitle</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.regions { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px; }
.region { padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
.badge { background: #6c757d; color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; float: right; }
</style>";
echo "</head><body>";

echo "<h1>$pageTitle</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// Debug: Show regions columns
echo "<h2>Debug: Regions Table</h2>";
$cols = $connection->query("SHOW COLUMNS FROM regions");
if ($cols) {
    echo "<p>Columns: ";
    $col_names = [];
    while ($col = $cols->fetch_assoc()) {
        $col_names[] = $col['Field'];
    }
    echo implode(', ', $col_names);
    echo "</p>";
}

// The SQL query
$sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
echo "<p>SQL Query: <code>$sql</code></p>";

$result = $connection->query($sql);

if ($result) {
    echo "<p style='color: green;'>✅ Regions query successful - Found " . $result->num_rows . " regions</p>";
    
    echo "<div class='regions'>";
    $displayed_count = 0;
    
    while ($row = $result->fetch_assoc()) {
        // Count institutions in this region
        $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE $regionColumn = {$row['id_region']}";
        $count_result = $connection->query($count_sql);
        
        if ($count_result) {
            $count_row = $count_result->fetch_assoc();
            $institution_count = $count_row['count'];
            
            if ($institution_count > 0) {
                $displayed_count++;
                echo "<div class='region'>";
                echo "<a href='$linkPrefix/{$row['region_name_en']}'>";
                echo htmlspecialchars($row['region_name']);
                echo "<span class='badge'>$institution_count</span>";
                echo "</a>";
                echo "</div>";
            }
        } else {
            echo "<p style='color: red;'>Count error for region {$row['id_region']}: " . $connection->error . "</p>";
        }
    }
    
    echo "</div>";
    
    if ($displayed_count == 0) {
        echo "<p>В данный момент нет доступных учебных заведений.</p>";
    } else {
        echo "<p>Показано регионов: $displayed_count</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Query failed: " . $connection->error . "</p>";
    echo "<p>Error details: " . mysqli_error($connection) . "</p>";
}

echo "<hr>";
echo "<p><a href='/'>← На главную</a> | <a href='/spo-all-regions'>SPO страница с ошибкой</a></p>";

echo "</body></html>";
$connection->close();
?>