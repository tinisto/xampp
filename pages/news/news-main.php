<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = "Новости образования";
$pageDescription = "Актуальные новости ВУЗов, ССУЗов, школ и образовательной сферы России";
$mainContent = 'pages/news/news-main-content.php';

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'metaD' => $pageDescription,
    'metaK' => 'новости образования, вузы, школы, ссузы, образование россия'
];

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);