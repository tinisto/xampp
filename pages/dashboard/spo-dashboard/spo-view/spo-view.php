<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/spo-dashboard/spo-view/spo-view-content.php";
$pageTitle = "Admin SPO";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template.php";
renderTemplate($pageTitle, $mainContent);