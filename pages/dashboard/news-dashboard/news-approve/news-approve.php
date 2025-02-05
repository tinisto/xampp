<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/news-dashboard/news-approve/news-approve-content.php";
$pageTitle = "Admin Approve NEWS";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template-engine-dashboard.php";
renderTemplate($pageTitle, $mainContent);
