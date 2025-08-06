<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

$pageTitle = 'Онлайн тесты';
$metaD = 'Пройдите бесплатные онлайн тесты по различным предметам: IQ тест, математика, русский язык, профориентация и многое другое';

// Page configuration
$pageConfig = [
    'metaD' => $metaD,
    'pageHeader' => [
        'title' => 'Онлайн тесты',
        'showSearch' => false
    ]
];

// Render the page using the unified template
renderTemplate($pageTitle, 'pages/tests/tests-main-content.php', $pageConfig);
?>