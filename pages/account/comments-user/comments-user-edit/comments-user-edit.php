<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";
$mainContent =  $_SERVER["DOCUMENT_ROOT"] . "/pages/account/comments-user/comments-user-edit/comments-user-edit-form.php";
$pageTitle = "Edit Comment - Admin Dashboard";
include $_SERVER["DOCUMENT_ROOT"] . "/common-components/template.php";
renderTemplate($pageTitle, $mainContent);
