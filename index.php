<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/template/template_config.php';

// Use the unified template system
PageLayouts::contentPage(
    '11-классники',
    'index_content.php',
    [
        'description' => 'Образовательный портал для учеников 11 классов. Новости образования, подготовка к ЕГЭ, выбор вуза.',
        'keywords' => '11 класс, ЕГЭ, образование, школа, университет, поступление'
    ]
);
