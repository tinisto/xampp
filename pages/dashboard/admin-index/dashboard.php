<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

$mainContent = "dashboard-content-modern.php";
$pageTitle = "Admin Dashboard";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template-engine.php";
renderTemplate($pageTitle, $mainContent);
