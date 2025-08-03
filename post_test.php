<?php
// Direct test file accessible at /post_test.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Post Test Page</h1>";

// Test 1: Check if we can load the database
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
echo "<p>✓ Database connection loaded</p>";

// Test 2: Check if post exists
$url = 'kogda-ege-ostalis-pozadi';
$stmt = $connection->prepare("SELECT * FROM posts WHERE url_post = ?");
$stmt->bind_param("s", $url);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<p>✓ Post found: " . htmlspecialchars($row['title_post']) . "</p>";
    
    // Test 3: Check template engine
    echo "<p>Testing template engine...</p>";
    
    $pageTitle = $row['title_post'];
    $testContent = 'post_test_content.php';
    
    // Create test content file
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/post_test_content.php', 
        '<div class="container"><h2>Test Content</h2><p>If you see header and footer around this, template is working!</p></div>');
    
    // Include template engine
    include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
    
    echo "<p>About to render template...</p>";
    
    // Render template
    
// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $testContent, $templateConfig);
    
} else {
    echo "<p>✗ Post not found!</p>";
}

$stmt->close();
?>