<?php
// Production error handling - comment out for development
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$mainContent = 'index_content_posts_with_news_style.php';
$pageTitle = 'Поиск школ, вузов, статей';

// Template configuration for modern homepage
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'no-bootstrap',
    'darkMode' => true,
    'showSearch' => true,
    'searchPlaceholder' => 'Поиск школ, вузов, статей...',
    'searchAction' => '/search-process',
    'searchName' => 'query'
];

// Include ultimate template engine
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Render the template with dynamic content
renderTemplate($pageTitle, $mainContent, $templateConfig);
?>