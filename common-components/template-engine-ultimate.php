<?php
// Redirect to the local template
// All files should use template.php instead
// This file is kept for backward compatibility

// Map old function names to new template
function renderTemplate($pageTitle, $mainContent, $additionalData = [], $metaD = null, $metaK = null) {
    // Set up variables for new template
    global $page_title, $headerContent, $navigationContent, $metadataContent, $filtersContent, $mainContent, $paginationContent, $commentsContent;
    
    $page_title = $pageTitle;
    
    // If mainContent is a file path, include it
    if (is_string($mainContent) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent)) {
        ob_start();
        include $_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent;
        $mainContent = ob_get_clean();
    }
    
    // Set default content sections
    $headerContent = '';
    $navigationContent = '';
    $metadataContent = '';
    $filtersContent = '';
    $paginationContent = '';
    $commentsContent = '';
    
    // Include the new template
    require_once $_SERVER['DOCUMENT_ROOT'] . '/template.php';
}

function renderAuthLayout($pageTitle, $mainContent, $config = []) {
    renderTemplate($pageTitle, $mainContent, $config);
}
?>