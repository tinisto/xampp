<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Regions Table Structure Check</h1>";

// Check if regions table exists
$check_table = mysqli_query($connection, "SHOW TABLES LIKE 'regions'");
if (mysqli_num_rows($check_table) > 0) {
    echo "<p>✅ Regions table exists</p>";
    
    // Get table structure
    echo "<h2>Table Structure:</h2>";
    $structure = mysqli_query($connection, "DESCRIBE regions");
    
    if ($structure) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = mysqli_fetch_assoc($structure)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show sample data
        echo "<h2>Sample Data (first 3 rows):</h2>";
        $sample = mysqli_query($connection, "SELECT * FROM regions LIMIT 3");
        
        if ($sample && mysqli_num_rows($sample) > 0) {
            $first_row = mysqli_fetch_assoc($sample);
            mysqli_data_seek($sample, 0);
            
            echo "<table border='1' cellpadding='5'>";
            echo "<tr>";
            foreach (array_keys($first_row) as $col) {
                echo "<th>" . $col . "</th>";
            }
            echo "</tr>";
            
            while ($row = mysqli_fetch_assoc($sample)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Check for specific columns
        echo "<h2>Column Check:</h2>";
        $columns_to_check = ['id', 'region_id', 'id_region'];
        
        foreach ($columns_to_check as $col) {
            $check = mysqli_query($connection, "SHOW COLUMNS FROM regions LIKE '$col'");
            if (mysqli_num_rows($check) > 0) {
                echo "<p>✅ Column '$col' EXISTS in regions table</p>";
            } else {
                echo "<p>❌ Column '$col' NOT FOUND in regions table</p>";
            }
        }
        
    } else {
        echo "<p>❌ Could not get table structure: " . mysqli_error($connection) . "</p>";
    }
} else {
    echo "<p>❌ Regions table does not exist!</p>";
}
?>