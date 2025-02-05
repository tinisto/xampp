<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Determine the type of institution based on the URL
$type = isset($_GET['type']) ? $_GET['type'] : 'schools';
$pageTitle = '';
$mainContent = 'educational-institutions-all-regions-content.php';
$table = '';
$countField = '';
$linkPrefix = '';

switch ($type) {
    case 'schools':
        $pageTitle = 'Школы в регионах России';
        $table = 'schools';
        $countField = 'school_count';
        $linkPrefix = 'schools-in-region';
        break;
    case 'spo':
        $pageTitle = 'Среднее профессиональное образование в регионах России';
        $table = 'spo';
        $countField = 'spo_count';
        $linkPrefix = 'spo-in-region';
        break;
    case 'vpo':
        $pageTitle = 'Высшее образование в регионах России';
        $table = 'vpo';
        $countField = 'vpo_count';
        $linkPrefix = 'vpo-in-region';
        break;
    default:
        header("Location: /error");
        exit();
}

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';
renderTemplate($pageTitle, $mainContent, [], '', '', $table, $countField, $linkPrefix);
?>
