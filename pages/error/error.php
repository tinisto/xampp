<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/SessionManager.php';
SessionManager::start();

$pageTitle = 'Ошибка - 11-классники';
$metaD = 'Произошла ошибка. Пожалуйста, попробуйте позже.';
$metaK = 'ошибка, error, 11классники';

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'metaD' => $metaD,
    'metaK' => $metaK
];

// Main content
$mainContent = 'pages/error/error-content.php';

// Render template
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);
?>