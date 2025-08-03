<?php
// Migrate categories data to new columns
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Migrating Categories Data</h2>";

// First, let's see what data we have
echo "<h3>Current data in categories table:</h3>";
$check = $connection->query("SELECT id, category_name, url_slug, title_category, url_category FROM categories LIMIT 10");
if ($check && $check->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>category_name</th><th>url_slug</th><th>title_category</th><th>url_category</th></tr>";
    while ($row = $check->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['url_slug']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title_category'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['url_category'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Copy data from existing columns to new columns
echo "<h3>Copying data to new columns...</h3>";

$update_sql = "UPDATE categories SET 
               title_category = category_name,
               url_category = url_slug
               WHERE title_category IS NULL OR title_category = ''";

if ($connection->query($update_sql)) {
    $affected = $connection->affected_rows;
    echo "<p>✅ Updated $affected categories</p>";
} else {
    echo "<p>❌ Error updating: " . $connection->error . "</p>";
}

// Verify the migration
echo "<h3>Verification - Categories after migration:</h3>";
$verify = $connection->query("SELECT url_category, title_category FROM categories ORDER BY title_category");
if ($verify && $verify->num_rows > 0) {
    echo "<p>✅ Categories are now properly configured:</p>";
    echo "<ul>";
    while ($row = $verify->fetch_assoc()) {
        echo "<li><a href='/category/" . htmlspecialchars($row['url_category']) . "'>" . 
             htmlspecialchars($row['title_category']) . "</a></li>";
    }
    echo "</ul>";
    echo "<p><strong>Total categories: " . $verify->num_rows . "</strong></p>";
} else {
    echo "<p>❌ No categories found after migration</p>";
}

// Check if the header query will work now
echo "<h3>Testing header query:</h3>";
$queryCategories = "SELECT url_category, title_category FROM categories ORDER BY title_category";
$resultCategories = mysqli_query($connection, $queryCategories);

if ($resultCategories && mysqli_num_rows($resultCategories) > 0) {
    echo "<p>✅ Header query works! Found " . mysqli_num_rows($resultCategories) . " categories</p>";
    echo "<p>The categories dropdown in the header should now work properly.</p>";
} else {
    echo "<p>❌ Header query failed</p>";
}

$connection->close();
?>