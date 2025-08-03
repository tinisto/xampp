<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include 'school-single-data-fetch.php';
$mainContent = 'pages/school/school-single-content-modern.php';
$metaD = $pageTitle . ' – образовательное учреждение, предоставляющее высококачественное образование. Узнайте больше о наших программах и возможностях обучения.';
$metaK = $pageTitle . ', образование, обучение, школьники, 11-классники, адрес, руководство, директор, новости, сайт, электронная почта';
$additionalData = ['row' => $row];
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
