<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include 'fetch-data-from-regions-table.php';

// Determine the type of institution based on the URL
$type = isset($_GET['type']) ? $_GET['type'] : 'spo';

// Set the page title based on the type of institution
switch ($type) {
    case 'schools':
        $pageTitle = 'Школы в регионе ' . $myrow_region['region_name'];
        break;
    case 'spo':
        $pageTitle = 'СПО в регионе ' . $myrow_region['region_name'];
        break;
    case 'vpo':
        $pageTitle = 'ВПО в регионе ' . $myrow_region['region_name'];
        break;
    default:
        header("Location: /error");
        exit();
}

// Template configuration
$mainContent = 'pages/common/educational-institutions-in-region/educational-institutions-in-region-content.php';
$additionalData = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'pageHeader' => [
        'title' => $pageTitle,
        'showSearch' => false
    ],
    'region_id' => $region_id,
    'type' => $type
];

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $additionalData);
