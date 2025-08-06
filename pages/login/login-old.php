<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php'; // File removed

$mainContent = 'login_content_safe.php';
$pageTitle = 'Вход - 11-классники';

// Template configuration for authorization layout
$templateConfig = [
    'layoutType' => 'auth',
    'cssFramework' => 'bootstrap',
    'darkMode' => true,
    'noHeader' => true,
    'noFooter' => true
];

// Use ultimate template engine with auth configuration
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);