<?php
// Enhanced debug for category issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$categorySlug = isset($_GET['category']) ? $_GET['category'] : 'a-naposledok-ya-skazhu';

echo "<h1>Enhanced Category Debug</h1>";
echo "<p>Testing category: <strong>" . htmlspecialchars($categorySlug) . "</strong></p>";
echo "<hr>";

// 1. Check if category-new.php exists
echo "<h2>1. File Check</h2>";
$files = [
    '/category-new.php' => 'Router file',
    '/pages/category/category.php' => 'Category display file',
    '/real_template.php' => 'Template file'
];

foreach ($files as $file => $desc) {
    $path = $_SERVER['DOCUMENT_ROOT'] . $file;
    if (file_exists($path)) {
        echo "<p>✓ {$desc}: <code>{$file}</code> exists (" . filesize($path) . " bytes)</p>";
    } else {
        echo "<p style='color:red;'>✗ {$desc}: <code>{$file}</code> NOT FOUND</p>";
    }
}

// 2. Database Structure Check
echo "<h2>2. Database Structure</h2>";
echo "<h3>Categories Table Structure:</h3>";
$result = $connection->query("DESCRIBE categories");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Category Data
echo "<h2>3. Category Data</h2>";
$query = "SELECT * FROM categories WHERE url_category = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $categorySlug);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if ($category) {
    echo "<p>✓ Category found!</p>";
    echo "<pre>";
    print_r($category);
    echo "</pre>";
    
    // Get the ID field name
    $categoryId = isset($category['id_category']) ? $category['id_category'] : (isset($category['id']) ? $category['id'] : null);
    echo "<p>Category ID: <strong>" . $categoryId . "</strong></p>";
    
    // 4. Posts for this category
    echo "<h2>4. Posts in Category</h2>";
    if ($categoryId) {
        $postsQuery = "SELECT id, title_post, category, date_post, url_slug 
                       FROM posts 
                       WHERE category = ? 
                       ORDER BY date_post DESC 
                       LIMIT 10";
        $stmt = $connection->prepare($postsQuery);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $postsResult = $stmt->get_result();
        
        echo "<p>Posts found: <strong>" . $postsResult->num_rows . "</strong></p>";
        
        if ($postsResult->num_rows > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Title</th><th>Category</th><th>URL</th><th>Date</th></tr>";
            while ($post = $postsResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $post['id'] . "</td>";
                echo "<td>" . htmlspecialchars($post['title_post']) . "</td>";
                echo "<td>" . $post['category'] . "</td>";
                echo "<td>" . htmlspecialchars($post['url_slug']) . "</td>";
                echo "<td>" . $post['date_post'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
} else {
    echo "<p style='color:red;'>✗ Category not found!</p>";
}

// 5. All Categories
echo "<h2>5. All Categories (for header dropdown)</h2>";
$allCats = $connection->query("SELECT * FROM categories ORDER BY title_category");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Title</th><th>URL</th><th>Link</th></tr>";
while ($cat = $allCats->fetch_assoc()) {
    $catId = isset($cat['id_category']) ? $cat['id_category'] : $cat['id'];
    echo "<tr>";
    echo "<td>" . $catId . "</td>";
    echo "<td>" . htmlspecialchars($cat['title_category']) . "</td>";
    echo "<td>" . htmlspecialchars($cat['url_category']) . "</td>";
    echo "<td><a href='/category/" . htmlspecialchars($cat['url_category']) . "'>Test Link</a></td>";
    echo "</tr>";
}
echo "</table>";

// 6. Posts Distribution
echo "<h2>6. Posts Distribution by Category</h2>";
$distQuery = "SELECT category, COUNT(*) as count FROM posts GROUP BY category ORDER BY count DESC LIMIT 20";
$distResult = $connection->query($distQuery);
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Category ID</th><th>Post Count</th></tr>";
while ($row = $distResult->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['category'] . "</td>";
    echo "<td>" . $row['count'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 7. Direct Include Test
echo "<h2>7. Direct Include Test</h2>";
echo "<p>Attempting to set variables and include template...</p>";
?>

<div style="margin: 20px; padding: 20px; border: 2px solid #333;">
    <h3>Test Links:</h3>
    <ul>
        <li><a href="/category/a-naposledok-ya-skazhu">Category: А напоследок я скажу</a></li>
        <li><a href="/category-new.php?category_en=a-naposledok-ya-skazhu">Direct Router Link</a></li>
        <li><a href="/debug-category.php">Basic Debug</a></li>
    </ul>
</div>