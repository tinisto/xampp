<?php
// VPO All Regions router - updated
error_reporting(0);

// Set type for the page
$_GET['type'] = 'vpo';
$institutionType = 'vpo';

// Include the main template version
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Fallback
    $headerContent = '<div style="padding: 30px;"><h1>ВПО всех регионов</h1></div>';
    $navigationContent = '';
    $metadataContent = '';
    $filtersContent = '';
    $mainContent = '<div style="padding: 20px;"><p>Страница временно недоступна</p></div>';
    $paginationContent = '';
    $commentsContent = '';
    $pageTitle = 'ВПО всех регионов - 11-классники';
    
    include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
}
?>