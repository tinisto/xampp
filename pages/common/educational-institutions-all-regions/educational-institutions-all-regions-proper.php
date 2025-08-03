<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Get the type from URL parameter
$type = $_GET['type'] ?? 'schools';

// Define table and field names based on type
switch ($type) {
    case 'spo':
        $table = 'spo';
        $linkPrefix = '/spo-in-region';
        $pageTitle = 'СПО по регионам';
        $metaD = 'Средние профессиональные образовательные учреждения (СПО) по регионам России';
        $metaK = 'СПО, колледжи, техникумы, регионы, среднее профессиональное образование';
        break;
    case 'vpo':
        $table = 'vpo';
        $linkPrefix = '/vpo-in-region';
        $pageTitle = 'ВПО по регионам';
        $metaD = 'Высшие учебные заведения (ВПО) по регионам России';
        $metaK = 'ВПО, университеты, институты, регионы, высшее образование';
        break;
    default: // schools
        $table = 'schools';
        $linkPrefix = '/schools-in-region';
        $pageTitle = 'Школы по регионам';
        $metaD = 'Школы по регионам России';
        $metaK = 'школы, регионы, среднее образование';
        break;
}

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'darkMode' => true,
    'metaD' => $metaD,
    'metaK' => $metaK,
    'table' => $table,
    'linkPrefix' => $linkPrefix,
    'pageTitle' => $pageTitle
];

// Content file path
$contentFile = 'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content-proper.php';

// Include the ONE TEMPLATE ENGINE
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $contentFile, $templateConfig, $metaD, $metaK);
?>