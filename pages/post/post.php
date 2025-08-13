<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/template/template_config.php';

include 'post-data-fetch.php';

// Use the unified template system
PageLayouts::contentPage(
    $pageTitle,
    'post-content.php',
    [
        'description' => $metaD,
        'keywords' => $metaK
    ]
);
