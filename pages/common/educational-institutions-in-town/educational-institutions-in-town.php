<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
$mainContent = 'educational-institutions-in-town-content.php';
$pageTitle = '';
$additionalData = '';
$metaD = '';
$metaK = '';

// Include the content file to set the variables
include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-in-town/educational-institutions-in-town-content.php';

// Include the template engine
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Render the template

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK);
?>
