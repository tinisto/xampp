<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to capture any early output
ob_start();

echo "<!-- Diagnostic: Starting news page -->\n";

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<!-- Diagnostic: check_under_construction.php loaded -->\n";

$pageTitle = "Новости образования";
$pageDescription = "Актуальные новости ВУЗов, ССУЗов, школ и образовательной сферы России";
$mainContent = 'pages/news/news-main-content.php';
$additionalData = [
    'metaD' => $pageDescription,
    'metaK' => 'новости образования, вузы, школы, ссузы, образование россия'
];

echo "<!-- Diagnostic: About to include template engine -->\n";

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

echo "<!-- Diagnostic: Template engine included -->\n";
echo "<!-- Diagnostic: About to call renderTemplate -->\n";

// Flush any output before rendering
$diagnosticOutput = ob_get_clean();

// Now render the template

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $templateConfig);

// If we get here but see nothing, add diagnostic
echo "\n<!-- Diagnostic: renderTemplate completed -->\n";
echo $diagnosticOutput;