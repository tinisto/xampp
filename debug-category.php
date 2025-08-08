<?php
// Debug category data
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$categorySlug = 'a-naposledok-ya-skazhu';

echo "<h2>Debug Category: " . htmlspecialchars($categorySlug) . "</h2>";

// Check if category exists
$query = "SELECT * FROM categories WHERE url_category = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $categorySlug);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if ($category) {
    echo "<h3>Category Found:</h3>";
    echo "<pre>";
    print_r($category);
    echo "</pre>";
    
    // Check posts for this category
    $postsQuery = "SELECT id, title_post, category, date_post FROM posts WHERE category = ? LIMIT 10";
    $stmt = $connection->prepare($postsQuery);
    $stmt->bind_param("i", $category['id_category']);
    $stmt->execute();
    $postsResult = $stmt->get_result();
    
    echo "<h3>Posts in this category:</h3>";
    if ($postsResult->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Category ID</th><th>Date</th></tr>";
        while ($post = $postsResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $post['id'] . "</td>";
            echo "<td>" . htmlspecialchars($post['title_post']) . "</td>";
            echo "<td>" . $post['category'] . "</td>";
            echo "<td>" . $post['date_post'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No posts found for category ID: " . $category['id_category'] . "</p>";
    }
    
} else {
    echo "<p>Category not found!</p>";
    
    // Show all categories
    echo "<h3>Available categories:</h3>";
    $allCats = $connection->query("SELECT id_category, title_category, url_category FROM categories");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>URL</th></tr>";
    while ($cat = $allCats->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $cat['id_category'] . "</td>";
        echo "<td>" . htmlspecialchars($cat['title_category']) . "</td>";
        echo "<td>" . htmlspecialchars($cat['url_category']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check total posts in database
$totalPosts = $connection->query("SELECT COUNT(*) as total FROM posts")->fetch_assoc()['total'];
echo "<h3>Total posts in database: " . $totalPosts . "</h3>";

// Show sample posts
echo "<h3>Sample posts:</h3>";
$samplePosts = $connection->query("SELECT id, title_post, category FROM posts LIMIT 5");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Title</th><th>Category ID</th></tr>";
while ($post = $samplePosts->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $post['id'] . "</td>";
    echo "<td>" . htmlspecialchars($post['title_post']) . "</td>";
    echo "<td>" . $post['category'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>