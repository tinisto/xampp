<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameter
$url_post = isset($_GET['url_post']) ? mysqli_real_escape_string($connection, $_GET['url_post']) : '';

if (empty($url_post)) {
    header("Location: /404");
    exit();
}

// Fetch post data
$query = "SELECT * FROM posts WHERE url_post = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $url_post);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $pageTitle = $row['title_post'];
    $postData = $row;
    $metaD = $row['meta_d_post'] ?? '';
    $metaK = $row['meta_k_post'] ?? '';
} else {
    header("Location: /404");
    exit();
}

mysqli_stmt_close($stmt);

// Include the unified template
$mainContent = 'post-content.php';
$additionalData = ['postData' => $postData];

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $templateConfig);