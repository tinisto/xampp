<?php

$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/schools-dashboard/schools-approve-edit/schools-approve-edit-content.php";
$pageTitle = "Schools editing approval";

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
