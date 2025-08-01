<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
// Database connection is already included in check_under_construction.php

// Include the news data fetch logic
include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-data-fetch.php';

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'metaD' => $metaD,
    'metaK' => $metaK,
    'newsData' => $newsData,
    'urlNews' => $urlNews,
];

// Render template
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, 'pages/common/news/news-content.php', $templateConfig);
