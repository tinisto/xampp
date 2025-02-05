<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include 'fetch-data-from-regions-table.php';

// Determine the type of institution based on the URL
$type = isset($_GET['type']) ? $_GET['type'] : 'spo';

// Set the page title based on the type of institution
switch ($type) {
    case 'schools':
        $pageTitle = 'Школы ' . $myrow_region['region_name_rod'];
        break;
    case 'spo':
        $pageTitle = 'Колледжи / Техникумы ' . $myrow_region['region_name_rod'];
        break;
    case 'vpo':
        $pageTitle = 'Высшие учебные заведения ' . $myrow_region['region_name_rod'];
        break;
    default:
        header("Location: /error");
        exit();
}

$mainContent = 'educational-institutions-in-region-content.php';
$additionalData = ['region_id' => $region_id, 'type' => $type];
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';
renderTemplate($pageTitle, $mainContent, $additionalData);
?>
