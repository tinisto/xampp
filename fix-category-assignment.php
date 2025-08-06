<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Fixing Category Assignment</h2>";

// First, find the ID of the 11-классники category
$category_query = "SELECT id_category FROM categories WHERE url_category = '11-klassniki'";
$category_result = mysqli_query($connection, $category_query);

if ($category_result && mysqli_num_rows($category_result) > 0) {
    $category_row = mysqli_fetch_assoc($category_result);
    $category_id = $category_row['id_category'];
    
    echo "<p>Found 11-классники category with ID: " . $category_id . "</p>";
    
    // Count posts currently with category = 1
    $count_query = "SELECT COUNT(*) as count FROM posts WHERE category = 1";
    $count_result = mysqli_query($connection, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    
    echo "<p>Posts with category = 1: " . $count_row['count'] . "</p>";
    
    // Update posts from category = 1 to the proper category ID
    $update_query = "UPDATE posts SET category = " . $category_id . " WHERE category = 1";
    
    if (mysqli_query($connection, $update_query)) {
        $affected_rows = mysqli_affected_rows($connection);
        echo "<p style='color: green;'>✓ Updated " . $affected_rows . " posts to category ID " . $category_id . "</p>";
    } else {
        echo "<p style='color: red;'>Error updating posts: " . mysqli_error($connection) . "</p>";
    }
    
    // Verify the update
    $verify_query = "SELECT COUNT(*) as count FROM posts WHERE category = " . $category_id;
    $verify_result = mysqli_query($connection, $verify_query);
    $verify_row = mysqli_fetch_assoc($verify_result);
    
    echo "<p>Posts now in 11-klassniki category: " . $verify_row['count'] . "</p>";
    
    // Show some example posts
    $sample_query = "SELECT title_post, url_slug FROM posts WHERE category = " . $category_id . " ORDER BY date_post DESC LIMIT 5";
    $sample_result = mysqli_query($connection, $sample_query);
    
    if ($sample_result && mysqli_num_rows($sample_result) > 0) {
        echo "<h3>Sample posts in 11-классники category:</h3>";
        echo "<ul>";
        while ($post = mysqli_fetch_assoc($sample_result)) {
            echo "<li><a href='/post/" . htmlspecialchars($post['url_slug']) . "'>" . htmlspecialchars($post['title_post']) . "</a></li>";
        }
        echo "</ul>";
    }
    
} else {
    echo "<p style='color: red;'>11-klassniki category not found. Please run add-category-simple.php first.</p>";
}
?>