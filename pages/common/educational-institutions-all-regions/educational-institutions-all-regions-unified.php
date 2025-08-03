<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Determine the type of institution based on the URL
$type = isset($_GET['type']) ? $_GET['type'] : 'schools';
$pageTitle = '';
$table = '';
$linkPrefix = '';

switch ($type) {
    case 'schools':
        $pageTitle = 'Школы в регионах России';
        $table = 'schools';
        $linkPrefix = 'schools-in-region';
        break;
    case 'spo':
        $pageTitle = 'Среднее профессиональное образование в регионах России';
        $table = 'spo';
        $linkPrefix = 'spo-in-region';
        break;
    case 'vpo':
        $pageTitle = 'Высшее образование в регионах России';
        $table = 'vpo';
        $linkPrefix = 'vpo-in-region';
        break;
    default:
        header("Location: /error");
        exit();
}

// Set up for template engine
$mainContent = 'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content-modern.php';
$additionalData = [
    'type' => $type,
    'pageTitle' => $pageTitle,
    'table' => $table,
    'linkPrefix' => $linkPrefix,
    'metaD' => $pageTitle . ' - полный список регионов',
    'metaK' => $table . ', регионы россии, образование'
];

// Use the unified template engine
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $templateConfig);
?>