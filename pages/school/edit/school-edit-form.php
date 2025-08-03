<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = 'school-edit-form-content.php';
$pageTitle = 'Страница редактирования';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Template configuration
$templateConfig = [
    'layoutType' => 'auth',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $templateConfig);
