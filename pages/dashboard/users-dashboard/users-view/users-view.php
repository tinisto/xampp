<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/users-dashboard/users-view/admin-users-content.php";
$pageTitle = "Users";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template.php";
renderTemplate($pageTitle, $mainContent);
