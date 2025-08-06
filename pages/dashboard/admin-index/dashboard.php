<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php'; // File removed
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

$pageTitle = "Admin Dashboard";
$mainContent = "pages/dashboard/admin-index/dashboard-content.php";

// Template configuration for dashboard layout
$templateConfig = [
    'layoutType' => 'dashboard',
    'cssFramework' => 'custom',
    'darkMode' => true,
    'noHeader' => true,
    'noFooter' => true
];

// Use ultimate template engine with dashboard configuration
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);
