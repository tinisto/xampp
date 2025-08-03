<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

$pageTitle = "Admin Dashboard";
$mainContent = "pages/dashboard/admin-index/dashboard-content.php";

// Use minimal dashboard template without header/footer
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-dashboard-minimal.php';
renderDashboardTemplate($pageTitle, $mainContent);
