<?php
// Step by step debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Step by Step Category Debug</h1>";

// Set the category
$_GET['category_en'] = 'a-naposledok-ya-skazhu';
$categorySlug = $_GET['category_en'];

echo "<h2>Step 1: Get Category</h2>";
$query = "SELECT * FROM categories WHERE url_category = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $categorySlug);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    die("Category not found!");
}

echo "<p>✓ Category found: " . htmlspecialchars($category['title_category']) . "</p>";
$categoryId = isset($category['id_category']) ? $category['id_category'] : $category['id'];
echo "<p>Category ID: $categoryId</p>";

// Get posts count
echo "<h2>Step 2: Count Posts</h2>";
$countQuery = "SELECT COUNT(*) as total FROM posts WHERE category = ?";
$stmt = $connection->prepare($countQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$countResult = $stmt->get_result();
$totalPosts = $countResult->fetch_assoc()['total'];
echo "<p>Total posts: $totalPosts</p>";

// Get posts
echo "<h2>Step 3: Get Posts</h2>";
$page = 1;
$perPage = 16;
$offset = 0;

$postsQuery = "SELECT id, title_post, text_post, url_slug, date_post 
               FROM posts 
               WHERE category = ? 
               ORDER BY date_post DESC 
               LIMIT ? OFFSET ?";
$stmt = $connection->prepare($postsQuery);
$stmt->bind_param("iii", $categoryId, $perPage, $offset);
$stmt->execute();
$postsResult = $stmt->get_result();

echo "<p>Query returned: " . $postsResult->num_rows . " posts</p>";

$posts = [];
while ($row = $postsResult->fetch_assoc()) {
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
    echo "<p>- Post ID {$row['id']}: " . htmlspecialchars($row['title_post']) . "</p>";
}

echo "<p>Posts array count: " . count($posts) . "</p>";
echo "<p>Posts empty check: " . (empty($posts) ? 'YES EMPTY' : 'NOT EMPTY') . "</p>";

// Test conditions
echo "<h2>Step 4: Test Conditions</h2>";
echo "<p>count(\$posts) > 0: " . (count($posts) > 0 ? 'TRUE' : 'FALSE') . "</p>";
echo "<p>!empty(\$posts): " . (!empty($posts) ? 'TRUE' : 'FALSE') . "</p>";

// Now test the actual category.php condition
echo "<h2>Step 5: Simulate category.php</h2>";

// Start output buffer like category.php does
ob_start();

// This is the exact code from category.php
if (count($posts) > 0) {
    echo "<p style='color:green;'>ENTERING IF BLOCK - count(\$posts) > 0 is TRUE</p>";
    
    // Check if cards-grid.php exists
    $cardsPath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    echo "<p>Cards grid path: " . $cardsPath . "</p>";
    echo "<p>File exists: " . (file_exists($cardsPath) ? 'YES' : 'NO') . "</p>";
    
    if (file_exists($cardsPath)) {
        include_once $cardsPath;
        echo "<p>Included cards-grid.php</p>";
        echo "<p>function_exists('renderCardsGrid'): " . (function_exists('renderCardsGrid') ? 'YES' : 'NO') . "</p>";
        
        if (function_exists('renderCardsGrid')) {
            echo "<p>Calling renderCardsGrid...</p>";
            renderCardsGrid($posts, 'post', [
                'columns' => 4,
                'gap' => 20,
                'showBadge' => true
            ]);
            echo "<p>After renderCardsGrid</p>";
        }
    }
} else {
    echo "<p style='color:red;'>ENTERING ELSE BLOCK - count(\$posts) > 0 is FALSE</p>";
    echo '<div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-folder-open fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
            <p>В этой категории пока нет статей</p>
            <p style="margin-top: 10px; font-size: 14px;">Категория ID: ' . $categoryId . '</p>
          </div>';
}

$output = ob_get_clean();

echo "<h2>Step 6: Output Buffer Result</h2>";
echo "<p>Buffer length: " . strlen($output) . " characters</p>";
echo "<div style='border: 2px solid blue; padding: 10px;'>";
echo $output;
echo "</div>";

// Debug the posts array
echo "<h2>Step 7: Posts Array Debug</h2>";
echo "<pre>";
var_dump($posts);
echo "</pre>";
?>