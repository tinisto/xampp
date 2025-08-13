<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/news-dashboard/admin-view-news/admin-view-news-content.php";
$pageTitle = "Новости";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template-engine.php";
renderTemplate($pageTitle, $mainContent);
