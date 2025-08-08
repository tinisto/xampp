<?php
// Script to rename 'id' to 'id_category' in categories table
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Renaming 'id' to 'id_category' in categories table</h2>";

try {
    // Check current column name
    $checkQuery = "SHOW COLUMNS FROM categories LIKE 'id%'";
    $result = $connection->query($checkQuery);
    
    echo "<h3>Current columns starting with 'id':</h3>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['Field'] . " - " . $row['Type'] . "</li>";
    }
    echo "</ul>";
    
    // Check if 'id' column exists
    $hasId = false;
    $hasIdCategory = false;
    
    $result = $connection->query("SHOW COLUMNS FROM categories");
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] === 'id') $hasId = true;
        if ($row['Field'] === 'id_category') $hasIdCategory = true;
    }
    
    if ($hasId && !$hasIdCategory) {
        // Rename the column
        $renameQuery = "ALTER TABLE categories CHANGE COLUMN `id` `id_category` INT NOT NULL AUTO_INCREMENT";
        
        if ($connection->query($renameQuery)) {
            echo "<p style='color: green;'>✓ Successfully renamed 'id' to 'id_category'</p>";
        } else {
            echo "<p style='color: red;'>✗ Error renaming column: " . $connection->error . "</p>";
        }
    } else if ($hasIdCategory) {
        echo "<p style='color: blue;'>Column 'id_category' already exists - no changes needed</p>";
    } else if (!$hasId) {
        echo "<p style='color: orange;'>Column 'id' not found in categories table</p>";
    }
    
    // Show updated structure
    echo "<h3>Updated table structure:</h3>";
    $result = $connection->query("DESCRIBE categories");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>