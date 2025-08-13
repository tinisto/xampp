<?php
/**
 * Template Migration Helper
 * Helps convert old template usage to new unified system
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

/**
 * Legacy template function wrapper
 * Provides backward compatibility while encouraging migration
 */
function renderTemplate($pageTitle, $mainContent, $additionalData = [], $metaD = "", $metaK = "") {
    // Include new template config
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/template/template_config.php';
    
    // Convert to new system format
    $config = new PageConfig([
        'title' => $pageTitle,
        'contentFile' => $mainContent,
        'description' => is_array($metaD) ? implode(", ", $metaD) : $metaD,
        'keywords' => is_array($metaK) ? implode(", ", $metaK) : $metaK,
        'contentData' => $additionalData
    ]);
    
    // Use original template engine for now
    require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';
    
    // Call original function with parameters
    renderTemplateOriginal($pageTitle, $mainContent, $additionalData, $metaD, $metaK);
}

/**
 * Migration suggestions
 */
function suggestMigration($currentFile) {
    $suggestions = [
        'index.php' => 'PageLayouts::contentPage()',
        'post.php' => 'PageLayouts::contentPage()',
        'admin/*.php' => 'PageLayouts::adminPage()',
        'test*.php' => 'PageLayouts::minimalPage()',
        'dashboard*.php' => 'PageLayouts::adminPage()'
    ];
    
    foreach ($suggestions as $pattern => $suggestion) {
        if (fnmatch($pattern, basename($currentFile))) {
            return $suggestion;
        }
    }
    
    return 'PageLayouts::contentPage()';
}
?>