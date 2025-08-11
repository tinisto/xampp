<?php
// Check towns table structure
require_once 'config/loadEnv.php';

// Create direct MySQL connection
$connection = new mysqli('127.0.0.1', 'root', 'root', '11klassniki_claude');

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

try {
    // Show table structure
    $query = "DESCRIBE towns";
    $result = mysqli_query($connection, $query);
    
    echo "<h2>Towns Table Structure:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Error: " . mysqli_error($connection) . "</td></tr>";
    }
    echo "</table>";
    
    // Show sample data
    echo "<h2>Sample Data (5 rows):</h2>";
    $query2 = "SELECT * FROM towns LIMIT 5";
    $result2 = mysqli_query($connection, $query2);
    
    if ($result2 && mysqli_num_rows($result2) > 0) {
        echo "<table border='1'>";
        // Header
        $firstRow = mysqli_fetch_assoc($result2);
        echo "<tr>";
        foreach ($firstRow as $key => $value) {
            echo "<th>" . $key . "</th>";
        }
        echo "</tr>";
        
        // First row data
        echo "<tr>";
        foreach ($firstRow as $value) {
            echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
        }
        echo "</tr>";
        
        // Rest of the data
        while ($row = mysqli_fetch_assoc($result2)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No data found or error: " . mysqli_error($connection);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

mysqli_close($connection);
?>