<?php
// Check categories table
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Categories Table Check</h2>";

// Check if categories table exists
$tables = $connection->query("SHOW TABLES LIKE 'categories'");
if ($tables->num_rows > 0) {
    echo "<p>✅ Categories table exists</p>";
    
    // Show structure
    echo "<h3>Table structure:</h3>";
    $structure = $connection->query("DESCRIBE categories");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($col = $structure->fetch_assoc()) {
        echo "<tr>";
        foreach ($col as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // Count categories
    $count = $connection->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc();
    echo "<p><strong>Total categories: " . $count['total'] . "</strong></p>";
    
    // Show sample data
    echo "<h3>Sample categories:</h3>";
    $sample = $connection->query("SELECT * FROM categories ORDER BY name ASC LIMIT 10");
    if ($sample->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        $first = true;
        while ($row = $sample->fetch_assoc()) {
            if ($first) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    echo "<th>$key</th>";
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
    } else {
        echo "<p>No categories found in table</p>";
    }
    
    // Check news categories
    echo "<h3>News table category usage:</h3>";
    $news_cats = $connection->query("SELECT DISTINCT category FROM news WHERE category IS NOT NULL LIMIT 10");
    if ($news_cats->num_rows > 0) {
        echo "<p>Sample category values in news table:</p>";
        echo "<ul>";
        while ($row = $news_cats->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($row['category']) . "</li>";
        }
        echo "</ul>";
    }
    
} else {
    echo "<p>❌ Categories table does not exist</p>";
}

// Check what's in the header file
echo "<h3>Header file location:</h3>";
$header_path = $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php';
if (file_exists($header_path)) {
    echo "<p>✅ Header file exists at: " . $header_path . "</p>";
    echo "<p>File size: " . filesize($header_path) . " bytes</p>";
} else {
    echo "<p>❌ Header file not found</p>";
}

$connection->close();
?>