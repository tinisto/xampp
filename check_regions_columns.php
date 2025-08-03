<?php
// Check regions table columns
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

echo "<h2>Regions Table Structure</h2>";

// Check if regions table exists
$tables = $connection->query("SHOW TABLES LIKE 'regions'");
if ($tables->num_rows == 0) {
    echo "<p style='color: red;'>❌ 'regions' table does not exist!</p>";
    
    // Check what tables do exist
    echo "<h3>Available tables:</h3>";
    $all_tables = $connection->query("SHOW TABLES");
    while ($table = $all_tables->fetch_row()) {
        echo $table[0] . "<br>";
    }
} else {
    // Show columns in regions table
    $columns = $connection->query("SHOW COLUMNS FROM regions");
    
    echo "<h3>Columns in 'regions' table:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($col = $columns->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . $col['Default'] . "</td>";
        echo "<td>" . $col['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show sample data
    echo "<h3>Sample data from regions table:</h3>";
    $sample = $connection->query("SELECT * FROM regions LIMIT 5");
    
    if ($sample->num_rows > 0) {
        $first = true;
        echo "<table border='1' cellpadding='5'>";
        
        while ($row = $sample->fetch_assoc()) {
            if ($first) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    echo "<th>" . $key . "</th>";
                }
                echo "</tr>";
                $first = false;
            }
            
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}

$connection->close();
?>