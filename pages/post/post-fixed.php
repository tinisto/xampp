<?php
// Ensure no output before this point
if (ob_get_level() == 0) ob_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Clear any accidental output
ob_clean();

// Include data fetch
include 'post-data-fetch.php';

// Set up content path
$mainContent = 'pages/post/post-content.php';

// Include and render template
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

// End output buffering
ob_end_flush();
?>