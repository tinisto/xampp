<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
include 'category-data-fetch.php';
$mainContent = 'pages/category/category-content-unified.php';
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',  // Use unified CSS framework
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'metaD' => $metaD,
    'metaK' => $metaK,
    'categoryData' => $categoryData
];
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);
