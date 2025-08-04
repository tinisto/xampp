<?php
// Add image_news column to news table
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check_admin.php';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

echo "<!DOCTYPE html>";
echo "<html><head><title>Add Image Column</title></head><body>";
echo "<h1>Adding image_news Column</h1>";

// Check if column already exists
$checkQuery = "SHOW COLUMNS FROM news LIKE 'image_news'";
$checkResult = $connection->query($checkQuery);

if ($checkResult && $checkResult->num_rows > 0) {
    echo "<p style='color: green;'>✅ Column 'image_news' already exists!</p>";
} else {
    // Add the column
    $alterQuery = "ALTER TABLE news ADD COLUMN image_news VARCHAR(255) DEFAULT NULL AFTER url_news";
    
    if ($connection->query($alterQuery)) {
        echo "<p style='color: green;'>✅ Successfully added 'image_news' column!</p>";
    } else {
        echo "<p style='color: red;'>❌ Error adding column: " . htmlspecialchars($connection->error) . "</p>";
    }
}

// Also check and add image_post column to posts table
echo "<h2>Checking posts table...</h2>";
$checkPostQuery = "SHOW COLUMNS FROM posts LIKE 'image_post'";
$checkPostResult = $connection->query($checkPostQuery);

if ($checkPostResult && $checkPostResult->num_rows > 0) {
    echo "<p style='color: green;'>✅ Column 'image_post' already exists!</p>";
} else {
    // Add the column
    $alterPostQuery = "ALTER TABLE posts ADD COLUMN image_post VARCHAR(255) DEFAULT NULL AFTER url_post";
    
    if ($connection->query($alterPostQuery)) {
        echo "<p style='color: green;'>✅ Successfully added 'image_post' column!</p>";
    } else {
        echo "<p style='color: red;'>❌ Error adding column: " . htmlspecialchars($connection->error) . "</p>";
    }
}

// Update the existing news item with the image
echo "<h2>Updating existing news with images...</h2>";

// Map of news IDs to their images (based on what you've uploaded)
$imageUpdates = [
    619 => '/uploads/content/news_689089eecbe64.png', // dsadas
    // Add more mappings as needed
];

foreach ($imageUpdates as $newsId => $imagePath) {
    $updateQuery = "UPDATE news SET image_news = ? WHERE id_news = ?";
    $stmt = $connection->prepare($updateQuery);
    $stmt->bind_param("si", $imagePath, $newsId);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Updated news ID $newsId with image</p>";
    } else {
        echo "<p style='color: red;'>❌ Error updating news ID $newsId: " . htmlspecialchars($stmt->error) . "</p>";
    }
    $stmt->close();
}

echo "<p><a href='/news/dsadas'>View the news item</a></p>";
echo "<p><a href='/dashboard'>Back to Dashboard</a></p>";

echo "</body></html>";
?>