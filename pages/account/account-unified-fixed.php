<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Get user data
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

$userId = $_SESSION['user_id'];
$occupation = $_SESSION["occupation"] ?? '';

// Fetch counts
$commentsCount = 0;
$newsCount = 0;

$stmt = $connection->prepare("SELECT COUNT(*) as count FROM comments WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$commentsCount = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

$stmt = $connection->prepare("SELECT COUNT(*) as count FROM news WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$newsCount = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// Include the unified template
$pageTitle = 'Личный кабинет';
$mainContent = 'pages/account/account-content.php';
$layoutType = 'dashboard';
$additionalData = [
    'commentsCount' => $commentsCount,
    'newsCount' => $newsCount,
    'occupation' => $occupation,
    'layoutType' => 'dashboard'
];

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $templateConfig);