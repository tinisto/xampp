<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = 'Учебное заведение';
$metaD = 'Информация об учебном заведении';
$metaK = 'учебное заведение, образование, ВУЗ, СПО';

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'metaD' => $metaD,
    'metaK' => $metaK,
];

// Render template
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, 'pages/common/vpo-spo/single-data-fetch.php', $templateConfig);
