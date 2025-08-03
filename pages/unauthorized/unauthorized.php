<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
$mainContent = 'unauthorized-content.php';
$pageTitle = 'Доступ ограничен';

// Template configuration
$templateConfig = [
    'layoutType' => 'minimal',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

// Render template
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate('Доступ ограничен', 'unauthorized-content.php', $templateConfig);
