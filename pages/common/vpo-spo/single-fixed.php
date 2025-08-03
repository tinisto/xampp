<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Determine the type (vpo or spo) based on the URL
$requestUri = $_SERVER['REQUEST_URI'];
$type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
$entityType = $type === 'vpo' ? 'university' : 'college';
$idEntityField = $type === 'vpo' ? 'id_vpo' : 'id_spo';
$urlField = $type === 'vpo' ? 'vpo_url' : 'spo_url';
$imagePrefix = $type === 'vpo' ? 'vpo' : 'spo';
$editFormUrl = $type === 'vpo' ? '/vpo-edit-form.php' : '/spo-edit-form.php';
$deleteFunction = $type === 'vpo' ? 'deleteVPO' : 'deleteSPO';
$sendEmailsUrl = '/pages/common/vpo-spo/send_emails.php';

include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/vpo-spo/single-data-fetch.php';

if (isset($pageTitle)) {
    // Map VPO/SPO database fields to template expected fields
    if (isset($row)) {
        if ($type === 'vpo') {
            // Map VPO fields to expected template fields
            $row['text'] = $row['director_info'] ?? '';
            $row['phone'] = $row['tel'] ?? '';
            $row['website'] = $row['site'] ?? '';
            $row['director'] = $row['director_name'] ?? '';
            $row['branches'] = $row['filials_vpo'] ?? '';
            $row['parent_institution'] = ''; // Could be populated later if needed
            // Build address from components
            $addressParts = array_filter([
                $row['zip_code'] ?? '',
                $row['city'] ?? '',
                $row['street'] ?? ''
            ]);
            $row['address'] = implode(', ', $addressParts);
        } else {
            // Map SPO fields to expected template fields
            $row['text'] = $row['director_info'] ?? '';
            $row['phone'] = $row['tel'] ?? '';
            $row['website'] = $row['site'] ?? '';
            $row['director'] = $row['director_name'] ?? '';
            $row['branches'] = $row['filials_spo'] ?? '';
            $row['parent_institution'] = ''; // Could be populated later if needed
            // Build address from components
            $addressParts = array_filter([
                $row['zip_code'] ?? '',
                $row['city'] ?? '',
                $row['street'] ?? ''
            ]);
            $row['address'] = implode(', ', $addressParts);
        }
    }
    
    $additionalData = ['row' => $row]; // Ensure $additionalData is defined  
    $mainContent = 'pages/common/vpo-spo/single-content-modern.php';

    $metaD = $pageTitle . ' – образовательное учреждение, предоставляющее высококачественное образование. Узнайте больше о наших программах и возможностях обучения.';
    $metaK = $pageTitle . ', ' . strtoupper($type) . ', образование, профессиональное обучение, студенты, адрес, руководство, директор, приемная комиссия, новости, сайт, электронная почта';
    
    // Template configuration - USE BOOTSTRAP LIKE SCHOOL PAGES
    $templateConfig = [
        'layoutType' => 'default',
        'cssFramework' => 'bootstrap', // Changed from 'custom' to 'bootstrap'
        'headerType' => 'modern',
        'footerType' => 'modern',
        'darkMode' => true,
        'metaD' => $metaD,
        'metaK' => $metaK,
        'row' => $row,
        'additionalData' => $additionalData
    ];
    
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
    renderTemplate($pageTitle, $mainContent, $templateConfig);
} else {
    header("Location: /404");
    exit();
}
?>