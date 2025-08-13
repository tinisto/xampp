<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/posts-dashboard/posts-view/posts-view-content.php";
$pageTitle = "Admin Posts";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template.php";
renderTemplate($pageTitle, $mainContent);
