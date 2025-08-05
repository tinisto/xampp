<?php
// Simple test to verify template engine is working
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get the URL parameter
$url_post = isset($_GET['url_post']) ? $_GET['url_post'] : '';

if (empty($url_post)) {
    die("No URL provided");
}

// Fetch post data
$stmt = $connection->prepare("SELECT * FROM posts WHERE url_slug = ?");
$stmt->bind_param("s", $url_post);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Set up page data
    $pageTitle = $row['title_post'];
    $postData = $row;
    $metaD = $row['meta_d_post'] ?? '';
    $metaK = $row['meta_k_post'] ?? '';
    
    // Output with template
    $mainContent = 'pages/post/post-content.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
    
// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $templateConfig);
} else {
    header("Location: /404");
    exit();
}

$stmt->close();
?>