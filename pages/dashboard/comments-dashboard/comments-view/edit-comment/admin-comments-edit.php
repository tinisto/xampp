<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent =  $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/comments-dashboard/comments-view/edit-comment/admin-comments-edit-form.php";
$pageTitle = "Edit Comment - Admin Dashboard";
include $_SERVER["DOCUMENT_ROOT"] . "/common-components/template-engine-dashboard.php";
renderTemplate($pageTitle, $mainContent);
