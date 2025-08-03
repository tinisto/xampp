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

// Include necessary functions
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
include 'function-query.php';

// Constants
$institutionsPerPage = 20;
$currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$pageOffset = max(0, ($currentPage - 1) * $institutionsPerPage);

// Fetch institutions
$institutions_result = getInstitutions($connection, $region_id, $type, $pageOffset, $institutionsPerPage);

// Get total count
$totalInstitutions_sql = "SELECT COUNT(*) AS total FROM $type WHERE id_region = ?";
$stmt_total = $connection->prepare($totalInstitutions_sql);
$stmt_total->bind_param("i", $region_id);
$stmt_total->execute();
$totalInstitutions_result = $stmt_total->get_result();
$totalInstitutions = $totalInstitutions_result->fetch_assoc()['total'];
$stmt_total->close();

// Define type details
$typeDetails = [
    'schools' => ['name' => 'Школы', 'field' => 'school_name', 'url' => 'school'],
    'vpo' => ['name' => 'ВУЗы', 'field' => 'vpo_name', 'url' => 'vpo'],
    'spo' => ['name' => 'ССУЗы', 'field' => 'spo_name', 'url' => 'spo']
];
$currentType = $typeDetails[$type] ?? $typeDetails['schools'];

// Prepare data for template
$additionalData = [
    'type' => $type,
    'region_id' => $region_id,
    'myrow_region' => $myrow_region,
    'institutions_result' => $institutions_result,
    'totalInstitutions' => $totalInstitutions,
    'institutionsPerPage' => $institutionsPerPage,
    'currentPage' => $currentPage,
    'currentType' => $currentType
];

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'type' => $type,
    'region_id' => $region_id,
    'myrow_region' => $myrow_region,
    'institutions_result' => $institutions_result,
    'totalInstitutions' => $totalInstitutions,
    'institutionsPerPage' => $institutionsPerPage,
    'currentPage' => $currentPage,
    'currentType' => $currentType
];

// Use ultimate template engine
$mainContent = 'pages/common/educational-institutions-in-region/educational-institutions-in-region-content-unified.php';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);
?>