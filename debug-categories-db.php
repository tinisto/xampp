<?php
/**
 * Debug Categories Database Structure
 * Check what categories and categories_news exist
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

echo "<h1>Categories Database Debug</h1>";

try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    echo "<h2>Database Tables:</h2>";
    $tables_query = "SHOW TABLES";
    $tables_result = $connection->query($tables_query);
    
    $category_tables = [];
    while ($row = $tables_result->fetch_row()) {
        $table_name = $row[0];
        if (strpos($table_name, 'categor') !== false) {
            $category_tables[] = $table_name;
            echo "<p>✓ Found table: <strong>$table_name</strong></p>";
        }
    }
    
    // Check each category table
    foreach ($category_tables as $table) {
        echo "<h3>Table: $table</h3>";
        
        // Get structure
        $structure_query = "DESCRIBE $table";
        $structure_result = $connection->query($structure_query);
        
        echo "<h4>Structure:</h4><ul>";
        while ($field = $structure_result->fetch_assoc()) {
            echo "<li>" . $field['Field'] . " (" . $field['Type'] . ")</li>";
        }
        echo "</ul>";
        
        // Get sample data
        $data_query = "SELECT * FROM $table LIMIT 10";
        $data_result = $connection->query($data_query);
        
        if ($data_result && $data_result->num_rows > 0) {
            echo "<h4>Sample Data:</h4><table border='1' style='border-collapse: collapse;'>";
            
            // Header
            $fields = $data_result->fetch_fields();
            echo "<tr>";
            foreach ($fields as $field) {
                echo "<th style='padding: 5px; background: #f0f0f0;'>" . $field->name . "</th>";
            }
            echo "</tr>";
            
            // Data
            $data_result->data_seek(0); // Reset to beginning
            while ($row = $data_result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td style='padding: 5px; border: 1px solid #ccc;'>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p><em>No data in this table</em></p>";
        }
        
        echo "<hr>";
    }
    
    // Check for education-news specifically
    echo "<h2>Checking for 'education-news' category:</h2>";
    foreach ($category_tables as $table) {
        $check_query = "SELECT * FROM $table WHERE url LIKE '%education-news%' OR title LIKE '%education%'";
        $check_result = $connection->query($check_query);
        
        if ($check_result && $check_result->num_rows > 0) {
            echo "<h4>Found in $table:</h4>";
            while ($row = $check_result->fetch_assoc()) {
                echo "<p>";
                foreach ($row as $key => $value) {
                    echo "<strong>$key:</strong> " . htmlspecialchars($value) . " | ";
                }
                echo "</p>";
            }
        } else {
            echo "<p>No 'education-news' found in $table</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3, h4 { color: #333; }
table { margin: 10px 0; }
</style>