<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/template/template_config.php';

include 'category-data-fetch.php';

// Use the unified template system
PageLayouts::contentPage(
    $pageTitle,
    'category-content.php',
    [
        'description' => $metaD,
        'keywords' => $metaK
    ]
);
