<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Category Page</h2>";

// Check if database connection file exists
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php')) {
    echo "✅ Database connection file exists<br>";
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (isset($connection)) {
        echo "✅ Database connection established<br>";
    } else {
        echo "❌ Database connection not established<br>";
    }
} else {
    echo "❌ Database connection file missing<br>";
}

// Check if check_under_construction file exists
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php')) {
    echo "✅ check_under_construction.php exists<br>";
} else {
    echo "❌ check_under_construction.php missing<br>";
}

// Check category files
$categoryFiles = [
    '/pages/category/category.php',
    '/pages/category/category-data-fetch.php',
    '/pages/category/category-content-unified.php',
    '/common-components/template-engine-ultimate.php',
    '/common-components/content-wrapper.php',
    '/common-components/page-header.php',
    '/common-components/typography.php',
    '/common-components/image-lazy-load.php',
    '/common-components/card-badge.php',
    '/includes/functions/pagination.php'
];

echo "<h3>Category System Files:</h3>";
foreach ($categoryFiles as $file) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test database query
if (isset($connection)) {
    echo "<h3>Testing Database Query:</h3>";
    $testQuery = "SELECT * FROM categories WHERE url_category = 'mir-uvlecheniy' LIMIT 1";
    $result = mysqli_query($connection, $testQuery);
    
    if ($result) {
        echo "✅ Query successful<br>";
        if (mysqli_num_rows($result) > 0) {
            echo "✅ Category 'mir-uvlecheniy' found in database<br>";
            $row = mysqli_fetch_assoc($result);
            echo "Category title: " . $row['title_category'] . "<br>";
        } else {
            echo "❌ Category 'mir-uvlecheniy' NOT found in database<br>";
        }
    } else {
        echo "❌ Query failed: " . mysqli_error($connection) . "<br>";
    }
}

phpinfo();