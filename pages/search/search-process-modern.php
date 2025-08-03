<?php
session_start();

// Check if a search query is set and not empty
if (!isset($_GET['query']) || empty($_GET['query'])) {
    header('Location: /search');
    exit();
}

$searchQuery = trim($_GET['query']);

// Basic validation - allow Cyrillic, Latin, numbers, and spaces
if (!preg_match("/^[\p{L}0-9\s]+$/u", $searchQuery)) {
    die("Недопустимый поисковый запрос.");
}

// Check length
if (mb_strlen($searchQuery) < 2) {
    die("Поисковый запрос слишком короткий. Введите минимум 2 символа.");
}

if (mb_strlen($searchQuery) > 100) {
    die("Поисковый запрос слишком длинный. Максимум 100 символов.");
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

if ($connection->connect_error) {
    die("Ошибка подключения к базе данных");
}

// Prepare content
$mainContent = 'pages/search/search-results-content.php';
$pageTitle = 'Поиск';
$additionalData = ['searchQuery' => $searchQuery, 'connection' => $connection];

// Include template engine
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Render the template

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $templateConfig);
?>