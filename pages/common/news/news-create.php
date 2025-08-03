<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

$currentDate = date("Y-m-d");

$isAdmin = $_SESSION['role'] == 'admin';
$pageTitle = $isAdmin ? "Создать новость Admin" : "Создать новость";
$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/common/news/news-create-form.php";

// Different meta tags for admin vs user
if ($isAdmin) {
    $metaDescription = "Create news as an admin";
    $metaKeywords = ["admin", "news", "create"];
    $additionalData = ['isAdmin' => true, 'currentDate' => $currentDate];  // Admin-specific data
} else {
    $metaDescription = "Create news as a user";
    $metaKeywords = ["user", "news", "create"];
    $additionalData = ['isAdmin' => false, 'currentDate' => $currentDate];  // User-specific data
}
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Template configuration
$templateConfig = [
    'layoutType' => 'auth',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $additionalData, $metaDescription, $metaKeywords);
