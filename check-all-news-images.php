<?php
// Check all news items and their images
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check_admin.php';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

echo "<!DOCTYPE html>";
echo "<html><head><title>Check All News Images</title></head><body>";
echo "<h1>All News Items with Images</h1>";

// Get all news items
$query = "SELECT id_news, title_news, url_news, approved, image_news FROM news ORDER BY date_news DESC LIMIT 20";
$result = $connection->query($query);

if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Title</th>";
    echo "<th>URL</th>";
    echo "<th>Status</th>";
    echo "<th>Image Path</th>";
    echo "<th>Image Preview</th>";
    echo "<th>View</th>";
    echo "</tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_news'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
        echo "<td>" . htmlspecialchars($row['url_news']) . "</td>";
        echo "<td>" . ($row['approved'] ? "Published" : "Draft") . "</td>";
        echo "<td>" . htmlspecialchars($row['image_news'] ?? 'NULL') . "</td>";
        echo "<td>";
        if (!empty($row['image_news'])) {
            echo "<img src='" . htmlspecialchars($row['image_news']) . "' alt='Preview' style='max-width: 100px; max-height: 100px;'>";
        } else {
            echo "No image";
        }
        echo "</td>";
        echo "<td><a href='/news/" . htmlspecialchars($row['url_news']) . "' target='_blank'>View</a></td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($connection->error) . "</p>";
}

// Check if image_news column exists
echo "<h2>Database Column Check:</h2>";
$checkQuery = "SHOW COLUMNS FROM news LIKE 'image_news'";
$checkResult = $connection->query($checkQuery);

if ($checkResult && $checkResult->num_rows > 0) {
    echo "<p style='color: green;'>✅ Column 'image_news' exists</p>";
    $columnInfo = $checkResult->fetch_assoc();
    echo "<pre>";
    print_r($columnInfo);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ Column 'image_news' does NOT exist</p>";
    echo "<p>Available columns in news table:</p>";
    $columnsQuery = "SHOW COLUMNS FROM news";
    $columnsResult = $connection->query($columnsQuery);
    echo "<ul>";
    while ($col = $columnsResult->fetch_assoc()) {
        echo "<li>" . $col['Field'] . " (" . $col['Type'] . ")</li>";
    }
    echo "</ul>";
}

echo "</body></html>";
?>