<?php
ensureAdminAuthenticated();

$mainContent = "schools-approve-edit-form-table.php";
$pageTitle = "Schools Verification";

// include 'template-engine.php';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Render the template with dynamic content

// Template configuration
$templateConfig = [
    'layoutType' => 'dashboard',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $templateConfig);
