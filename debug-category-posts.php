<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Debug Category and Posts</h2>";

// Check all categories with their IDs
echo "<h3>All Categories:</h3>";
$cat_query = "SELECT id_category, title_category, url_category FROM categories ORDER BY id_category";
$cat_result = mysqli_query($connection, $cat_query);

if ($cat_result && mysqli_num_rows($cat_result) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Title</th><th>URL</th></tr>";
    while ($cat = mysqli_fetch_assoc($cat_result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($cat['id_category']) . "</td>";
        echo "<td>" . htmlspecialchars($cat['title_category']) . "</td>";
        echo "<td>" . htmlspecialchars($cat['url_category']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check posts by category
echo "<h3>Posts by Category:</h3>";
$posts_query = "SELECT category, COUNT(*) as count FROM posts GROUP BY category ORDER BY category";
$posts_result = mysqli_query($connection, $posts_query);

if ($posts_result && mysqli_num_rows($posts_result) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Category ID</th><th>Post Count</th></tr>";
    while ($post = mysqli_fetch_assoc($posts_result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($post['category']) . "</td>";
        echo "<td>" . htmlspecialchars($post['count']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Show sample posts with category = 1
echo "<h3>Sample Posts with category = 1:</h3>";
$sample_query = "SELECT title_post, url_slug, category FROM posts WHERE category = 1 ORDER BY date_post DESC LIMIT 10";
$sample_result = mysqli_query($connection, $sample_query);

if ($sample_result && mysqli_num_rows($sample_result) > 0) {
    echo "<ul>";
    while ($sample = mysqli_fetch_assoc($sample_result)) {
        echo "<li>" . htmlspecialchars($sample['title_post']) . " (category: " . $sample['category'] . ")</li>";
    }
    echo "</ul>";
}

// Find the 11-klassniki category ID
$find_query = "SELECT id_category FROM categories WHERE url_category = '11-klassniki' OR title_category = '11-классники'";
$find_result = mysqli_query($connection, $find_query);

if ($find_result && mysqli_num_rows($find_result) > 0) {
    $find_row = mysqli_fetch_assoc($find_result);
    $target_id = $find_row['id_category'];
    
    echo "<h3>11-klassniki Category ID: " . $target_id . "</h3>";
    
    // Update posts from category = 1 to the correct category
    $update_query = "UPDATE posts SET category = " . $target_id . " WHERE category = 1";
    
    if (mysqli_query($connection, $update_query)) {
        $affected = mysqli_affected_rows($connection);
        echo "<p style='color: green;'>✓ Updated " . $affected . " posts to category " . $target_id . "</p>";
        
        // Verify update
        $verify_query = "SELECT COUNT(*) as count FROM posts WHERE category = " . $target_id;
        $verify_result = mysqli_query($connection, $verify_query);
        $verify_row = mysqli_fetch_assoc($verify_result);
        
        echo "<p>Posts now in 11-klassniki category: " . $verify_row['count'] . "</p>";
    } else {
        echo "<p style='color: red;'>Error updating posts: " . mysqli_error($connection) . "</p>";
    }
} else {
    echo "<p style='color: red;'>Could not find 11-klassniki category</p>";
}
?>