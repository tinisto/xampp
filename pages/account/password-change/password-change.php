<?php
// Page configuration
$pageConfig = [
    'title' => 'Изменить пароль',
    'showBackButton' => true
];

// Specify content file
$pageContent = 'password-change-fields.php';

// Template configuration for account sub-page
$mainContent = 'includes/account-template.php';
$pageTitle = 'Изменить пароль - 11-классники';
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'pageConfig' => $pageConfig,
    'pageContent' => $pageContent
];

// Include ultimate template
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);