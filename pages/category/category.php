<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
include 'category-data-fetch.php';
// Count posts in this category before passing to template
$countQuery = "SELECT COUNT(*) as count FROM posts WHERE category = ?";
$countStmt = mysqli_prepare($connection, $countQuery);
mysqli_stmt_bind_param($countStmt, 'i', $categoryData['id']);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$postCount = mysqli_fetch_assoc($countResult)['count'];
mysqli_stmt_close($countStmt);

// Format the count text
$badgeText = $postCount . ' ' . ($postCount == 1 ? 'статья' : ($postCount < 5 ? 'статьи' : 'статей'));

$mainContent = 'pages/category/category-content-unified.php';
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',  // Use unified CSS framework
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'metaD' => $metaD,
    'categoryData' => $categoryData,
    'pageHeader' => [
        'title' => $pageTitle,
        'showSearch' => false,
        'badge' => $badgeText
    ]
];
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);
