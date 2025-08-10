<?php
// Debug what the news page is actually outputting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Capture output from news.php
ob_start();

// Set up expected variables
$_SERVER['REQUEST_URI'] = '/news';
$_GET['page'] = 1;

// Include the news page
include $_SERVER['DOCUMENT_ROOT'] . '/news-new.php';

$output = ob_get_clean();

// Display analysis
echo "<h1>News Page Output Analysis</h1>";

// Check for loading message
$loadingPos = strpos($output, 'Новости загружаются');
if ($loadingPos !== false) {
    echo "<p style='color: red;'>⚠️ Found 'Новости загружаются' at position $loadingPos</p>";
    
    // Extract surrounding content
    $start = max(0, $loadingPos - 200);
    $excerpt = substr($output, $start, 400);
    echo "<h3>Context around loading message:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . htmlspecialchars($excerpt) . "</pre>";
} else {
    echo "<p style='color: green;'>✓ No loading message found</p>";
}

// Check for greyContent5
$content5Pos = strpos($output, 'greyContent5');
if ($content5Pos !== false) {
    echo "<p>Found greyContent5 reference at position $content5Pos</p>";
}

// Look for cards-grid output
$cardsGridPos = strpos($output, 'card');
if ($cardsGridPos !== false) {
    echo "<p style='color: green;'>✓ Found card elements at position $cardsGridPos</p>";
} else {
    echo "<p style='color: red;'>✗ No card elements found</p>";
}

// Check output length
echo "<p>Total output length: " . strlen($output) . " characters</p>";

// Look for specific patterns
$patterns = [
    'data-lazy-load' => 'Lazy load attributes',
    'loading-placeholder' => 'Loading placeholders',
    'skeleton' => 'Skeleton loaders',
    'news-container' => 'News container',
    'grid-template-columns' => 'Grid layout',
    '496' => 'Total news count'
];

echo "<h3>Pattern search:</h3>";
foreach ($patterns as $pattern => $description) {
    $found = strpos($output, $pattern) !== false;
    echo "<p>$description ('$pattern'): " . ($found ? "<span style='color: green;'>Found</span>" : "<span style='color: red;'>Not found</span>") . "</p>";
}

// Display first and last 1000 characters
echo "<h3>First 1000 characters of output:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . htmlspecialchars(substr($output, 0, 1000)) . "</pre>";

echo "<h3>Last 1000 characters of output:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . htmlspecialchars(substr($output, -1000)) . "</pre>";
?>