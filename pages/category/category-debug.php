<?php
// Debug version of category.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Category Debug</h1>";
echo "<pre>";
echo "GET parameters:\n";
print_r($_GET);
echo "\nURL: " . $_SERVER['REQUEST_URI'];
echo "\n</pre>";

// require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Check if the URL parameter is set
if (isset($_GET['category_en']) || isset($_GET['url_category'])) {
    // Sanitize the input
    if (isset($_GET['category_en'])) {
        $urlCategory = mysqli_real_escape_string($connection, $_GET['category_en']);
    } else {
        $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category']);
    }
    
    echo "<p>Looking for category: " . htmlspecialchars($urlCategory) . "</p>";
    
    // Fetch category data
    $queryCategory = "SELECT * FROM categories WHERE url_category = '$urlCategory'";
    $resultCategory = mysqli_query($connection, $queryCategory);
    
    if ($resultCategory) {
        echo "<p>Query executed successfully. Rows found: " . mysqli_num_rows($resultCategory) . "</p>";
        
        if (mysqli_num_rows($resultCategory) > 0) {
            $categoryData = mysqli_fetch_assoc($resultCategory);
            echo "<p>Category found:</p>";
            echo "<pre>";
            print_r($categoryData);
            echo "</pre>";
        } else {
            echo "<p>No category found with url_category = '" . htmlspecialchars($urlCategory) . "'</p>";
        }
    } else {
        echo "<p>Query error: " . mysqli_error($connection) . "</p>";
    }
} else {
    echo "<p>No category parameter found in URL</p>";
}
?>