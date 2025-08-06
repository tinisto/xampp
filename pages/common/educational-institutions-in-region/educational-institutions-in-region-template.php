<?php
// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameters from htaccess rewrite
$region_name_en = isset($_GET['region_name_en']) ? $_GET['region_name_en'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'spo';

// Validate type
if (!in_array($type, ['schools', 'spo', 'vpo'])) {
    header("Location: /404");
    exit();
}

// Get region data
$region_id = null;
$region_name = '';
if (!empty($region_name_en)) {
    $query_region = "SELECT id_region, region_name FROM regions WHERE region_name_en = ?";
    $stmt = $connection->prepare($query_region);
    if ($stmt) {
        $stmt->bind_param("s", $region_name_en);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $region_id = $row['id_region'];
            $region_name = $row['region_name'];
            // Set myrow_region for compatibility with content file
            $myrow_region = $row;
        }
        $stmt->close();
    }
}

// If no region found, redirect to 404
if (!$region_id) {
    header("Location: /404");
    exit();
}

// Set the page title based on the type of institution
switch ($type) {
    case 'schools':
        $pageTitle = 'Школы в регионе ' . $region_name;
        break;
    case 'spo':
        $pageTitle = 'СПО в регионе ' . $region_name;
        break;
    case 'vpo':
        $pageTitle = 'ВПО в регионе ' . $region_name;
        break;
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
    'type' => $type,
    'myrow_region' => $myrow_region
];

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $additionalData);
?>