<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Step 1: Starting category debug<br>";

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
    echo "Step 2: check_under_construction loaded<br>";
} catch (Exception $e) {
    echo "Error in step 2: " . $e->getMessage() . "<br>";
    exit;
}

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    echo "Step 3: database connection loaded<br>";
} catch (Exception $e) {
    echo "Error in step 3: " . $e->getMessage() . "<br>";
    exit;
}

echo "Step 4: URL parameter check<br>";
if (isset($_GET['url_category'])) {
    echo "URL category: " . $_GET['url_category'] . "<br>";
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category']);
    echo "Sanitized: " . $urlCategory . "<br>";
} else {
    echo "No url_category parameter<br>";
}

try {
    include 'category-data-fetch.php';
    echo "Step 5: category-data-fetch loaded<br>";
} catch (Exception $e) {
    echo "Error in step 5: " . $e->getMessage() . "<br>";
    exit;
}

if (isset($categoryData)) {
    echo "Step 6: categoryData exists<br>";
    echo "Category title: " . $categoryData['title_category'] . "<br>";
} else {
    echo "Step 6: categoryData not set<br>";
}

echo "Step 7: Template engine test<br>";
try {
    $mainContent = 'pages/category/category-content-modern.php';
    echo "Main content path: " . $mainContent . "<br>";
    
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent;
    echo "Full path: " . $fullPath . "<br>";
    
    if (file_exists($fullPath)) {
        echo "Content file exists<br>";
    } else {
        echo "Content file does NOT exist<br>";
    }
    
    include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-unified.php';
    echo "Step 8: template engine loaded<br>";
    
    renderTemplate($pageTitle, $mainContent, ['metaD' => $metaD, 'metaK' => $metaK, 'categoryData' => $categoryData]);
    
} catch (Exception $e) {
    echo "Error in template: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}