<?php
// Include data fetch file
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include 'school-single-data-fetch.php';

// Check if we have the data
if (!isset($row) || !isset($pageTitle)) {
    header("Location: /404");
    exit();
}

// Include the unified template
$mainContent = 'school-single-content.php';
$additionalData = ['row' => $row];

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