<?php
// Check SQLite towns table structure
require_once 'database/db_modern.php';

try {
    // Get columns for towns table
    $columns = db_fetch_all("PRAGMA table_info(towns)");
    
    echo "<h2>Towns Table Structure in SQLite:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>CID</th><th>Name</th><th>Type</th><th>Not Null</th><th>Default</th><th>Primary Key</th></tr>";
    
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['cid'] . "</td>";
        echo "<td>" . $col['name'] . "</td>";
        echo "<td>" . $col['type'] . "</td>";
        echo "<td>" . $col['notnull'] . "</td>";
        echo "<td>" . $col['dflt_value'] . "</td>";
        echo "<td>" . $col['pk'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show sample data
    echo "<h2>Sample Data (5 rows):</h2>";
    $data = db_fetch_all("SELECT * FROM towns LIMIT 5");
    
    if (!empty($data)) {
        echo "<table border='1'>";
        // Header
        echo "<tr>";
        foreach (array_keys($data[0]) as $key) {
            echo "<th>" . $key . "</th>";
        }
        echo "</tr>";
        
        // Data
        foreach ($data as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No data found in towns table</p>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>