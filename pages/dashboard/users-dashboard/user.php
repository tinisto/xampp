<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = "user-content.php";
$pageTitle = "User Details";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template.php";
renderTemplate($pageTitle, $mainContent);
