<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/vpo-dashboard/vpo-edit/vpo-edit-content.php";
$pageTitle = "Universities Verification";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template-engine.php";
renderTemplate($pageTitle, $mainContent);
