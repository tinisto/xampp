<?php
// Dashboard Demo - Showcases improved dashboard design without authentication
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Mock statistics for demo
$stats = [
    'schools' => 1247,
    'vpo' => 589,
    'spo' => 372,
    'users' => 15284,
    'comments' => 2891,
    'news' => 453,
    'posts' => 1672,
    'messages' => 78
];

$mainContent = 'dashboard-demo-content.php';
$pageTitle = 'Dashboard Demo - 11классники.ru';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';
renderUnifiedTemplate($pageTitle, $mainContent, [], "", "", "", "", "", 'dashboard');
?>