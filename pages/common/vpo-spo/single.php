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
    $additionalData = ['row' => $row]; // Ensure $additionalData is defined
    ob_start();
    include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/vpo-spo/single-content.php';
    $mainContent = ob_get_clean();

    $metaD = $pageTitle . ' – образовательное учреждение, предоставляющее высококачественное образование. Узнайте больше о наших программах и возможностях обучения.';
    $metaK = $pageTitle . ', ' . strtoupper($type) . ', образование, профессиональное обучение, студенты, адрес, руководство, директор, приемная комиссия, новости, сайт, электронная почта';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';
    renderTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK);
} else {
    header("Location: /404");
    exit();
}
?>
