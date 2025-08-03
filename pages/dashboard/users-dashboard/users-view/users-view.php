<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

$pageTitle = "Users";
$mainContent = "pages/dashboard/users-dashboard/users-view/admin-users-content.php";

// Use minimal dashboard template without header/footer
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-dashboard-minimal.php';
renderDashboardTemplate($pageTitle, $mainContent);
