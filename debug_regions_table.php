<?php
// Debug regions table issue
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Database Connection Debug</h2>";
echo "Connected to database: " . $connection->query("SELECT DATABASE()")->fetch_row()[0] . "<br><br>";

echo "<h2>Checking 'regions' table</h2>";

// Show all tables
echo "<h3>All tables in database:</h3>";
$tables_result = $connection->query("SHOW TABLES");
echo "<ul>";
while ($table = $tables_result->fetch_row()) {
    echo "<li>" . $table[0] . "</li>";
}
echo "</ul>";

// Check regions table structure
$regions_exists = $connection->query("SHOW TABLES LIKE 'regions'")->num_rows > 0;

if ($regions_exists) {
    echo "<h3>Structure of 'regions' table:</h3>";
    $columns = $connection->query("DESCRIBE regions");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($col = $columns->fetch_assoc()) {
        echo "<tr>";
        foreach ($col as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // Try the query
    echo "<h3>Testing query:</h3>";
    $sql = "SELECT id, region_name, region_name_en FROM regions WHERE country_id = 1 ORDER BY region_name ASC";
    echo "<p>Query: " . htmlspecialchars($sql) . "</p>";
    
    $result = $connection->query($sql);
    if ($result) {
        echo "<p style='color: green;'>✅ Query successful! Found " . $result->num_rows . " regions</p>";
        
        // Show first 5 regions
        echo "<h4>First 5 regions:</h4>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Region Name</th><th>Region Name EN</th></tr>";
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['region_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['region_name_en']) . "</td>";
            echo "</tr>";
            if (++$count >= 5) break;
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Query failed: " . $connection->error . "</p>";
    }
}

// Check if there's another regions-like table
echo "<h3>Looking for region-related tables:</h3>";
$region_tables = $connection->query("SHOW TABLES LIKE '%region%'");
if ($region_tables->num_rows > 0) {
    echo "<ul>";
    while ($table = $region_tables->fetch_row()) {
        echo "<li>" . $table[0];
        
        // Show structure of this table
        $cols = $connection->query("SHOW COLUMNS FROM " . $table[0]);
        $col_names = [];
        while ($col = $cols->fetch_assoc()) {
            $col_names[] = $col['Field'];
        }
        echo " (columns: " . implode(', ', $col_names) . ")";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No region-related tables found</p>";
}

// Test what the actual file is using
echo "<h3>Testing actual file location:</h3>";
$file_path = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php';
if (file_exists($file_path)) {
    echo "<p>✅ File exists at: " . $file_path . "</p>";
    echo "<p>File size: " . filesize($file_path) . " bytes</p>";
    echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($file_path)) . "</p>";
} else {
    echo "<p>❌ File not found at: " . $file_path . "</p>";
}

$connection->close();
?>