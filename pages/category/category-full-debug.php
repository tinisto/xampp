<?php
// Full debug version with error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Category Full Debug</h1>";

// Test 1: Check database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
echo "<h2>1. Database Connection</h2>";
if ($connection) {
    echo "<p style='color: green;'>✓ Database connected</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
}

// Test 2: Check category data fetch
echo "<h2>2. Category Data Fetch</h2>";
include 'category-data-fetch.php';

if (isset($categoryData)) {
    echo "<p style='color: green;'>✓ Category data loaded</p>";
    echo "<pre>";
    print_r($categoryData);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>✗ Category data not loaded</p>";
}

// Test 3: Check if template variables are set
echo "<h2>3. Template Variables</h2>";
echo "<p>Page Title: " . (isset($pageTitle) ? htmlspecialchars($pageTitle) : "NOT SET") . "</p>";
echo "<p>Main Content: " . (isset($mainContent) ? $mainContent : "NOT SET") . "</p>";

// Test 4: Try to render template
echo "<h2>4. Template Rendering</h2>";
try {
    $mainContent = 'pages/category/category-content-unified.php';
    $templateConfig = [
        'layoutType' => 'default',
        'cssFramework' => 'custom',
        'headerType' => 'modern',
        'footerType' => 'modern',
        'darkMode' => true,
        'metaD' => $metaD ?? '',
        'metaK' => $metaK ?? '',
        'categoryData' => $categoryData ?? []
    ];
    
    echo "<p>Template config:</p>";
    echo "<pre>";
    print_r($templateConfig);
    echo "</pre>";
    
    // Check if template engine exists
    $templatePath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
    if (file_exists($templatePath)) {
        echo "<p style='color: green;'>✓ Template engine found</p>";
        
        // Check if content file exists
        $contentPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent;
        if (file_exists($contentPath)) {
            echo "<p style='color: green;'>✓ Content file found: $mainContent</p>";
        } else {
            echo "<p style='color: red;'>✗ Content file NOT found: $mainContent</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Template engine NOT found</p>";
    }
    
    echo "<hr><h2>Attempting to render...</h2>";
    
    // Actually try to render
    include $templatePath;
    renderTemplate($pageTitle, $mainContent, $templateConfig);
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>