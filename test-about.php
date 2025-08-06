<?php
// Test the about page without the missing include
$mainContent = 'pages/about/about_content.php';
$pageTitle = 'О сайте 11-классники';
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
    renderTemplate($pageTitle, $mainContent, $templateConfig);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>