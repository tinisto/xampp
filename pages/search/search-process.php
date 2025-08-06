<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Get search query for page title
$searchQuery = $_GET['query'] ?? '';
$searchQuery = trim($searchQuery);

$pageTitle = !empty($searchQuery) ? "Поиск: " . htmlspecialchars($searchQuery) : "Результаты поиска";
$mainContent = 'pages/search/search-process-content.php';

// Template configuration - pass searchQuery to content
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'metaD' => 'Результаты поиска по запросу "' . htmlspecialchars($searchQuery) . '" - 11-классники',
    'searchQuery' => $searchQuery  // Pass search query to template
];

// Include the template engine
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);
?>