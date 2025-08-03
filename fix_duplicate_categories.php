<?php
// Fix duplicate categories
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Fixing Duplicate Categories</h2>";

// Show duplicates
echo "<h3>Checking for duplicate categories:</h3>";
$duplicates = $connection->query("
    SELECT title_category, COUNT(*) as count, GROUP_CONCAT(id) as ids, GROUP_CONCAT(url_category) as urls
    FROM categories 
    GROUP BY title_category 
    HAVING count > 1
");

if ($duplicates && $duplicates->num_rows > 0) {
    echo "<p>Found duplicates:</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Category</th><th>Count</th><th>IDs</th><th>URLs</th><th>Action</th></tr>";
    
    while ($row = $duplicates->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['title_category']) . "</td>";
        echo "<td>{$row['count']}</td>";
        echo "<td>{$row['ids']}</td>";
        echo "<td>" . htmlspecialchars($row['urls']) . "</td>";
        echo "<td>";
        
        // For "Абитуриентам", keep the one with 'abiturientam' slug
        if ($row['title_category'] === 'Абитуриентам') {
            $ids = explode(',', $row['ids']);
            $urls = explode(',', $row['urls']);
            
            for ($i = 0; $i < count($ids); $i++) {
                if ($urls[$i] === 'for-applicants') {
                    $delete_id = $ids[$i];
                    echo "Will delete ID $delete_id (for-applicants)";
                    
                    // Delete the one with 'for-applicants'
                    $delete_sql = "DELETE FROM categories WHERE id = $delete_id";
                    if ($connection->query($delete_sql)) {
                        echo " - ✅ Deleted";
                    } else {
                        echo " - ❌ Error: " . $connection->error;
                    }
                }
            }
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>✅ No duplicate categories found</p>";
}

// Show all categories after cleanup
echo "<h3>All categories after cleanup:</h3>";
$all_cats = $connection->query("SELECT id, title_category, url_category FROM categories ORDER BY title_category");

if ($all_cats && $all_cats->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>URL</th><th>Test Link</th></tr>";
    
    while ($cat = $all_cats->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$cat['id']}</td>";
        echo "<td>" . htmlspecialchars($cat['title_category']) . "</td>";
        echo "<td>" . htmlspecialchars($cat['url_category']) . "</td>";
        echo "<td><a href='/category/" . htmlspecialchars($cat['url_category']) . "' target='_blank'>Test →</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><strong>Total categories: " . $all_cats->num_rows . "</strong></p>";
}

// Optional: Check for any news using the old category ID
echo "<h3>Checking news with deleted categories:</h3>";
$orphaned_news = $connection->query("
    SELECT COUNT(*) as count 
    FROM news 
    WHERE category NOT IN (SELECT id FROM categories)
    AND category IS NOT NULL
");

if ($orphaned_news) {
    $count = $orphaned_news->fetch_assoc();
    if ($count['count'] > 0) {
        echo "<p>⚠️ Found {$count['count']} news articles with non-existent category IDs</p>";
        echo "<p>These might need to be reassigned to valid categories.</p>";
    } else {
        echo "<p>✅ All news articles have valid category references</p>";
    }
}

$connection->close();
?>