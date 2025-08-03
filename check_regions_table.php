<?php
// Check regions table structure
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Regions Table Structure Check</h1>";

// Show columns
echo "<h2>Regions Table Columns:</h2>";
$cols = $connection->query("SHOW COLUMNS FROM regions");
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th></tr>";
while ($col = $cols->fetch_assoc()) {
    echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
}
echo "</table>";

// Test queries
echo "<h2>Test Queries:</h2>";

// Query 1: Using id
echo "<h3>1. SELECT * FROM regions WHERE country_id = 1 LIMIT 3</h3>";
$test1 = $connection->query("SELECT * FROM regions WHERE country_id = 1 LIMIT 3");
if ($test1) {
    echo "<p style='color: green;'>✅ Query successful</p>";
    echo "<table border='1'>";
    $first = true;
    while ($row = $test1->fetch_assoc()) {
        if ($first) {
            echo "<tr>";
            foreach (array_keys($row) as $key) {
                echo "<th>$key</th>";
            }
            echo "</tr>";
            $first = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>$value</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Query failed: " . $connection->error . "</p>";
}

// Fix the educational institutions pages
echo "<h2>Fix Required:</h2>";
echo "<p>The educational-institutions-all-regions.php file is using 'region_id' but the regions table uses 'id' as the primary key.</p>";

if (isset($_GET['fix']) && $_GET['fix'] == 'yes') {
    $file_path = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php';
    $content = file_get_contents($file_path);
    
    // Fix the SQL query
    $content = str_replace(
        "SELECT region_id, region_name, region_name_en FROM regions",
        "SELECT id, region_name, region_name_en FROM regions",
        $content
    );
    
    // Fix the references
    $content = str_replace(
        "\$row['region_id']",
        "\$row['id']",
        $content
    );
    
    // Fix the count query
    $content = str_replace(
        "WHERE \$regionColumn = {\$row['region_id']}",
        "WHERE \$regionColumn = {\$row['id']}",
        $content
    );
    
    // Fix the data attribute
    $content = str_replace(
        'data-region-id="<?= $row[\'region_id\'] ?>"',
        'data-region-id="<?= $row[\'id\'] ?>"',
        $content
    );
    
    if (file_put_contents($file_path, $content)) {
        echo "<p style='color: green;'>✅ Fixed educational-institutions-all-regions.php</p>";
    } else {
        echo "<p style='color: red;'>❌ Could not update file</p>";
    }
} else {
    echo "<p><a href='?fix=yes' style='background: green; color: white; padding: 10px 20px; text-decoration: none;'>Fix Region Column Names</a></p>";
}

$connection->close();
?>