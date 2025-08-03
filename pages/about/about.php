<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
$mainContent = 'pages/about/about_content.php';
$pageTitle = 'О сайте 11-классники';
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',  // Use unified CSS framework
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);
