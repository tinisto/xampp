<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages\dashboard\comments-dashboard\comments-view\admin-comments-content.php";
$pageTitle = "Comments";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template-engine.php";
renderTemplate($pageTitle, $mainContent);