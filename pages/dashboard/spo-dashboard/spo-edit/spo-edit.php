<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/spo-dashboard/spo-edit/spo-edit-content.php";
$pageTitle = "spo Verification";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template-engine-dashboard.php";
renderTemplate($pageTitle, $mainContent);
