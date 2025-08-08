<?php
// Simple test of category query
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Test Category Query</h1>";

// Test 1: Get category
$categorySlug = 'a-naposledok-ya-skazhu';
$query = "SELECT * FROM categories WHERE url_category = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $categorySlug);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

echo "<h2>Category:</h2>";
echo "<pre>";
print_r($category);
echo "</pre>";

// Get the ID
$categoryId = isset($category['id_category']) ? $category['id_category'] : $category['id'];
echo "<p>Category ID: $categoryId</p>";

// Test 2: Count posts
$countQuery = "SELECT COUNT(*) as total FROM posts WHERE category = ?";
$stmt = $connection->prepare($countQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$countResult = $stmt->get_result();
$totalPosts = $countResult->fetch_assoc()['total'];
echo "<p>Total posts with category = $categoryId: <strong>$totalPosts</strong></p>";

// Test 3: Get posts
$postsQuery = "SELECT id, title_post, category, url_slug FROM posts WHERE category = ? LIMIT 5";
$stmt = $connection->prepare($postsQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$postsResult = $stmt->get_result();

echo "<h2>Posts:</h2>";
if ($postsResult->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>Category</th><th>URL</th></tr>";
    while ($row = $postsResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . htmlspecialchars($row['title_post']) . "</td>";
        echo "<td>{$row['category']}</td>";
        echo "<td>" . htmlspecialchars($row['url_slug']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No posts found!</p>";
}

// Test 4: Check includes
echo "<h2>Include Test:</h2>";
$realTitlePath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
if (file_exists($realTitlePath)) {
    echo "<p>✓ real_title.php exists</p>";
} else {
    echo "<p>✗ real_title.php NOT FOUND</p>";
}

$cardsGridPath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
if (file_exists($cardsGridPath)) {
    echo "<p>✓ cards-grid.php exists</p>";
} else {
    echo "<p>✗ cards-grid.php NOT FOUND</p>";
}

// Test 5: Direct URL test
echo "<h2>Test Links:</h2>";
echo '<p><a href="/category/a-naposledok-ya-skazhu">Category page</a></p>';
echo '<p><a href="/debug-category-page.php?category_en=a-naposledok-ya-skazhu">Debug category page</a></p>';
?>