<?php
// Include input validator
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/input-validator.php';

// Get and validate search query
$searchQuery = InputValidator::validateSearchQuery($_GET['query'] ?? '');

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