<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/messages-dashboard/messages-view/messages-view-content.php";
$pageTitle = "Messages";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template-engine-dashboard.php";
renderTemplate($pageTitle, $mainContent);
