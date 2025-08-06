<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameter
$url_slug = isset($_GET['url_post']) ? mysqli_real_escape_string($connection, $_GET['url_post']) : '';

if (empty($url_slug)) {
    header("Location: /404");
    exit();
}

// Fetch post data - using url_slug field
$query = "SELECT * FROM posts WHERE url_slug = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $url_slug);
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

// Format author, date and views for header badge
$date = new DateTime($postData['date_post']);
$formattedDate = $date->format('d.m.Y');
$viewCount = number_format((int)$postData['view_post']);
$badgeText = htmlspecialchars($postData['author_post']) . ' • ' . $formattedDate . ' • ' . $viewCount . ' просмотров';

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',  // Use unified CSS framework
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'postData' => $postData,
    'metaD' => $metaD,
    'metaK' => $metaK,
    'pageHeader' => [
        'title' => $pageTitle,
        'showSearch' => false,
        'badge' => $badgeText
    ]
];

// Render template
// Use ultimate template engine (simplified - no fallbacks needed)
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, 'pages/post/post-content-professional.php', $templateConfig);
