<?php
// VPO All Regions router - updated
error_reporting(0);

// Set type for the page
$_GET['type'] = 'vpo';
$institutionType = 'vpo';

// Include the NEW real template version
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions-real.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Fallback
    $greyContent1 = '<div style="padding: 30px;"><h1>ВПО всех регионов</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 20px;"><p>Страница временно недоступна</p></div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'ВПО всех регионов - 11-классники';
    
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}
?>