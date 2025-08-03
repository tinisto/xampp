<?php
// Simple test to bypass the complex environment setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Direct database connection for testing
$connection = new mysqli('localhost', 'u2709849_default', 'JWBr0F_0', 'u2709849_default');

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$connection->set_charset('utf8mb4');

// Test if we can fetch the category
$urlCategory = 'mir-uvlecheniy';
$query = "SELECT * FROM categories WHERE url_category = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('s', $urlCategory);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<h2>Category Found!</h2>";
    echo "<pre>";
    print_r($row);
    echo "</pre>";
    
    // Now let's include the actual category page content
    $_GET['url_category'] = 'mir-uvlecheniy';
    $categoryData = $row;
    $pageTitle = $row['title_category'];
    $metaD = $row['meta_d_category'];
    $metaK = $row['meta_k_category'];
    
    // Include the content directly
    echo "<h3>Attempting to render category page...</h3>";
    
    // Set up minimal required functions
    function renderContentWrapper($mode) {
        if ($mode === 'start') {
            echo '<div class="content-wrapper"><div class="content-container">';
        } else {
            echo '</div></div>';
        }
    }
    
    function renderPageHeader($title, $subtitle = '', $options = []) {
        echo "<h1>$title</h1>";
        if ($subtitle) echo "<p>$subtitle</p>";
    }
    
    function renderCardBadge($text, $url = '', $position = 'overlay', $color = 'green') {
        echo "<span class='badge badge-$color'>$text</span>";
    }
    
    function renderLazyImage($options) {
        echo "<img src='{$options['src']}' alt='{$options['alt']}' class='{$options['class']}'>";
    }
    
    function renderCallout($content, $type = 'info', $title = '') {
        echo "<div class='callout callout-$type'>";
        if ($title) echo "<h4>$title</h4>";
        echo "<p>$content</p></div>";
    }
    
    function renderPagination($url, $current, $total) {
        echo "Page $current of $total";
    }
    
    // Try to include the category content
    include 'pages/category/category-content-unified.php';
    
} else {
    echo "Category not found!";
}

$stmt->close();
$connection->close();
?>