<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Update Posts to Category 21 (11-klassniki)</h2>";

// Check current posts with category = 1
$count_query = "SELECT COUNT(*) as count FROM posts WHERE category = 1";
$count_result = mysqli_query($connection, $count_query);
$count_row = mysqli_fetch_assoc($count_result);

echo "<p>Posts currently with category = 1: " . $count_row['count'] . "</p>";

// Update posts from category = 1 to category = 21
$update_query = "UPDATE posts SET category = 21 WHERE category = 1";

if (mysqli_query($connection, $update_query)) {
    $affected_rows = mysqli_affected_rows($connection);
    echo "<p style='color: green;'>✓ Successfully updated " . $affected_rows . " posts to category 21 (11-klassniki)</p>";
    
    // Verify the update
    $verify_query = "SELECT COUNT(*) as count FROM posts WHERE category = 21";
    $verify_result = mysqli_query($connection, $verify_query);
    $verify_row = mysqli_fetch_assoc($verify_result);
    
    echo "<p>Posts now in category 21: " . $verify_row['count'] . "</p>";
    
    // Show sample posts
    $sample_query = "SELECT title_post, url_slug FROM posts WHERE category = 21 ORDER BY date_post DESC LIMIT 5";
    $sample_result = mysqli_query($connection, $sample_query);
    
    if ($sample_result && mysqli_num_rows($sample_result) > 0) {
        echo "<h3>Sample posts now in 11-klassniki category:</h3>";
        echo "<ul>";
        while ($post = mysqli_fetch_assoc($sample_result)) {
            echo "<li><a href='/post/" . htmlspecialchars($post['url_slug']) . "' target='_blank'>" . htmlspecialchars($post['title_post']) . "</a></li>";
        }
        echo "</ul>";
    }
    
    echo "<p style='color: blue;'>Now check: <a href='/category/11-klassniki' target='_blank'>https://11klassniki.ru/category/11-klassniki</a></p>";
    
} else {
    echo "<p style='color: red;'>Error updating posts: " . mysqli_error($connection) . "</p>";
}

// Also need to update the homepage code to use category = 21 instead of category = 1
echo "<h3>Important:</h3>";
echo "<p style='color: orange;'>⚠️ The homepage code in index_content_posts_with_news_style.php needs to be updated to use category = 21 instead of category = 1</p>";
?>