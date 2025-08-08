<?php
// Debug version of category page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$categorySlug = isset($_GET['category_en']) ? $_GET['category_en'] : 'a-naposledok-ya-skazhu';

echo "<h1>Category Page Debug</h1>";
echo "<p>Category slug: " . htmlspecialchars($categorySlug) . "</p>";

// Get category data
$query = "SELECT * FROM categories WHERE url_category = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $categorySlug);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    echo "<p style='color:red;'>Category not found!</p>";
    exit;
}

echo "<h2>Category Data:</h2>";
echo "<pre>";
print_r($category);
echo "</pre>";

// Check which ID field exists
$categoryId = isset($category['id_category']) ? $category['id_category'] : (isset($category['id']) ? $category['id'] : null);
echo "<p>Using category ID: <strong>" . $categoryId . "</strong></p>";

// Get current page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 16;
$offset = ($page - 1) * $perPage;

echo "<h2>Pagination:</h2>";
echo "<p>Page: $page, Per page: $perPage, Offset: $offset</p>";

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM posts WHERE category = ?";
echo "<p>Count query: " . htmlspecialchars($countQuery) . "</p>";
$stmt = $connection->prepare($countQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$countResult = $stmt->get_result();
$totalPosts = $countResult->fetch_assoc()['total'];
echo "<p>Total posts found: <strong>$totalPosts</strong></p>";

// Get posts
$postsQuery = "SELECT id, title_post, text_post, url_slug, date_post 
               FROM posts 
               WHERE category = ? 
               ORDER BY date_post DESC 
               LIMIT ? OFFSET ?";
echo "<h2>Posts Query:</h2>";
echo "<p>" . htmlspecialchars($postsQuery) . "</p>";
echo "<p>Parameters: category=$categoryId, limit=$perPage, offset=$offset</p>";

$stmt = $connection->prepare($postsQuery);
$stmt->bind_param("iii", $categoryId, $perPage, $offset);
$stmt->execute();
$postsResult = $stmt->get_result();

echo "<h2>Posts Result:</h2>";
echo "<p>Number of posts returned: <strong>" . $postsResult->num_rows . "</strong></p>";

if ($postsResult->num_rows > 0) {
    echo "<h3>Posts Data:</h3>";
    $posts = [];
    while ($row = $postsResult->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<h4>" . htmlspecialchars($row['title_post']) . "</h4>";
        echo "<p>ID: " . $row['id'] . "</p>";
        echo "<p>URL: " . htmlspecialchars($row['url_slug']) . "</p>";
        echo "<p>Date: " . $row['date_post'] . "</p>";
        echo "<p>Text preview: " . htmlspecialchars(substr($row['text_post'], 0, 100)) . "...</p>";
        echo "</div>";
        
        $posts[] = [
            'id_news' => $row['id'],
            'title_news' => $row['title_post'],
            'url_news' => $row['url_slug'],
            'image_news' => file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$row['id']}_1.jpg") 
                ? "/images/posts-images/{$row['id']}_1.jpg" 
                : '/images/default-news.jpg',
            'created_at' => $row['date_post'],
            'category_title' => $category['title_category'],
            'category_url' => $category['url_category']
        ];
    }
    
    echo "<h3>Formatted Posts Array:</h3>";
    echo "<pre>";
    print_r($posts);
    echo "</pre>";
} else {
    echo "<p style='color:red;'>No posts returned from query!</p>";
    
    // Additional debug - check posts table directly
    echo "<h3>Checking posts table directly:</h3>";
    $debugQuery = "SELECT id, title_post, category FROM posts WHERE category = ? LIMIT 5";
    $stmt = $connection->prepare($debugQuery);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $debugResult = $stmt->get_result();
    
    if ($debugResult->num_rows > 0) {
        echo "<p>Found posts in direct query:</p>";
        while ($row = $debugResult->fetch_assoc()) {
            echo "<p>ID: {$row['id']}, Title: {$row['title_post']}, Category: {$row['category']}</p>";
        }
    } else {
        echo "<p>No posts found even in direct query!</p>";
    }
}

// Check the actual category.php file
echo "<h2>Category.php File Info:</h2>";
$categoryFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/category/category.php';
if (file_exists($categoryFile)) {
    echo "<p>File exists, size: " . filesize($categoryFile) . " bytes</p>";
    echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($categoryFile)) . "</p>";
} else {
    echo "<p style='color:red;'>Category.php file not found!</p>";
}
?>