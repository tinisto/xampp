<?php
// Debug version to check what's happening
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<!-- Debug: Starting post.php -->\n";

// Include the data fetch
include 'post-data-fetch.php';

echo "<!-- Debug: Data fetched, title: " . ($pageTitle ?? 'no title') . " -->\n";

// Set up for template
$mainContent = 'pages/post/post-content.php';

echo "<!-- Debug: About to call template engine -->\n";

// Include template engine
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

echo "<!-- Debug: Template engine included -->\n";

// Render the template

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $templateConfig);

echo "<!-- Debug: Template rendered -->\n";
?>