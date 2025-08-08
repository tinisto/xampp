<?php
// Final comprehensive debug for category issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Category Debug - Final Check</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

// Test 1: Direct component test
echo "<h2>1. Testing renderCardsGrid directly</h2>";

$testPosts = [
    [
        'id_news' => 413,
        'title_news' => 'Test Post 1',
        'url_news' => 'test-post-1',
        'image_news' => '/images/default-news.jpg',
        'created_at' => '2015-08-06',
        'category_title' => 'Test Category',
        'category_url' => 'test-category'
    ]
];

// Check component file
$cardsGridPath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
echo "<p>Cards grid path: " . htmlspecialchars($cardsGridPath) . "</p>";
if (file_exists($cardsGridPath)) {
    echo "<p>✓ File exists, size: " . filesize($cardsGridPath) . " bytes</p>";
    echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($cardsGridPath)) . "</p>";
    
    // Show first few lines of the file
    echo "<p>First 5 lines of cards-grid.php:</p>";
    echo "<pre style='background: #f0f0f0; padding: 10px;'>";
    $lines = file($cardsGridPath);
    for ($i = 0; $i < min(5, count($lines)); $i++) {
        echo htmlspecialchars($lines[$i]);
    }
    echo "</pre>";
    
    include_once $cardsGridPath;
    
    if (function_exists('renderCardsGrid')) {
        echo "<p>✓ renderCardsGrid function exists</p>";
        echo "<div style='border: 2px solid red; padding: 10px; margin: 10px 0;'>";
        echo "<p>Calling renderCardsGrid with test data...</p>";
        renderCardsGrid($testPosts, 'post');
        echo "</div>";
    } else {
        echo "<p style='color:red;'>✗ renderCardsGrid function NOT found!</p>";
    }
} else {
    echo "<p style='color:red;'>✗ cards-grid.php NOT found!</p>";
}

// Test 2: Check actual category page
echo "<h2>2. Testing actual category page flow</h2>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$categorySlug = 'a-naposledok-ya-skazhu';
$query = "SELECT * FROM categories WHERE url_category = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $categorySlug);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if ($category) {
    $categoryId = isset($category['id_category']) ? $category['id_category'] : $category['id'];
    echo "<p>Category found: ID = $categoryId</p>";
    
    // Get posts
    $postsQuery = "SELECT id, title_post, text_post, url_slug, date_post 
                   FROM posts 
                   WHERE category = ? 
                   LIMIT 5";
    $stmt = $connection->prepare($postsQuery);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $postsResult = $stmt->get_result();
    
    echo "<p>Posts query returned: " . $postsResult->num_rows . " posts</p>";
    
    $posts = [];
    while ($row = $postsResult->fetch_assoc()) {
        $posts[] = [
            'id_news' => $row['id'],
            'title_news' => $row['title_post'],
            'url_news' => $row['url_slug'],
            'image_news' => '/images/default-news.jpg',
            'created_at' => $row['date_post'],
            'category_title' => $category['title_category'],
            'category_url' => $category['url_category']
        ];
    }
    
    echo "<p>Posts array has " . count($posts) . " items</p>";
    
    if (!empty($posts)) {
        echo "<div style='border: 2px solid blue; padding: 10px; margin: 10px 0;'>";
        echo "<p>Rendering posts with renderCardsGrid...</p>";
        
        // Force output
        ob_start();
        if (function_exists('renderCardsGrid')) {
            renderCardsGrid($posts, 'post');
        } else {
            echo "<p style='color:red;'>renderCardsGrid not available!</p>";
        }
        $output = ob_get_clean();
        
        if (empty($output)) {
            echo "<p style='color:red;'>renderCardsGrid produced NO output!</p>";
        } else {
            echo "<p style='color:green;'>renderCardsGrid produced output:</p>";
            echo $output;
        }
        echo "</div>";
    }
}

// Test 3: Check what category.php is doing
echo "<h2>3. Simulating category.php output buffering</h2>";

ob_start();
echo "<p>This is test content in output buffer</p>";
if (!empty($posts)) {
    if (function_exists('renderCardsGrid')) {
        renderCardsGrid($posts, 'post', [
            'columns' => 4,
            'gap' => 20,
            'showBadge' => true
        ]);
    }
} else {
    echo '<div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-folder-open fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
            <p>В этой категории пока нет статей</p>
          </div>';
}
$bufferedContent = ob_get_clean();

echo "<div style='border: 2px solid green; padding: 10px;'>";
echo "<p>Output buffer captured " . strlen($bufferedContent) . " characters:</p>";
if (empty($bufferedContent)) {
    echo "<p style='color:red;'>Buffer is EMPTY!</p>";
} else {
    echo $bufferedContent;
}
echo "</div>";

// Test 4: Direct HTML output test
echo "<h2>4. Direct HTML output (no functions)</h2>";
if (!empty($posts)) {
    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; padding: 20px; border: 2px solid purple;">';
    foreach ($posts as $post) {
        echo '<div style="background: #f0f0f0; padding: 15px; border: 1px solid #ccc;">';
        echo '<h3>' . htmlspecialchars($post['title_news']) . '</h3>';
        echo '<p>ID: ' . $post['id_news'] . '</p>';
        echo '<p>URL: ' . htmlspecialchars($post['url_news']) . '</p>';
        echo '<p>Date: ' . $post['created_at'] . '</p>';
        echo '</div>';
    }
    echo '</div>';
}

echo "<h2>5. Links to test</h2>";
echo '<ul>';
echo '<li><a href="/category/a-naposledok-ya-skazhu">Actual category page</a></li>';
echo '<li><a href="/test-category-display.php">Test category display</a></li>';
echo '<li><a href="/test-cards-component.php">Test cards component</a></li>';
echo '</ul>';
?>