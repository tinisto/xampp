<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Categories Debug Test</h2>";

// Test 1: Check if environment variables are loaded
echo "<h3>1. Environment Variables</h3>";
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "<br>";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "<br>";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "<br>";
echo "DB_PASS: " . (defined('DB_PASS') ? '***HIDDEN***' : 'NOT DEFINED') . "<br>";

// Test 2: Database connection
echo "<h3>2. Database Connection</h3>";
try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($connection->connect_error) {
        echo "Connection failed: " . $connection->connect_error . "<br>";
    } else {
        echo "✅ Connection successful<br>";
        $connection->set_charset("utf8mb4");
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "<br>";
}

// Test 3: Categories table
echo "<h3>3. Categories Table</h3>";
if ($connection && !$connection->connect_error) {
    // Check if table exists
    $tableCheck = $connection->query("SHOW TABLES LIKE 'categories'");
    if ($tableCheck->num_rows > 0) {
        echo "✅ Categories table exists<br>";
        
        // Get table structure
        echo "<h4>Table Structure:</h4><pre>";
        $structure = $connection->query("DESCRIBE categories");
        while ($row = $structure->fetch_assoc()) {
            print_r($row);
        }
        echo "</pre>";
        
        // Get categories
        echo "<h4>Categories Data:</h4>";
        $queryCategories = "SELECT * FROM categories ORDER BY title_category";
        $resultCategories = mysqli_query($connection, $queryCategories);
        
        if ($resultCategories) {
            echo "Query successful. Row count: " . mysqli_num_rows($resultCategories) . "<br>";
            
            if (mysqli_num_rows($resultCategories) > 0) {
                echo "<ul>";
                while ($category = mysqli_fetch_assoc($resultCategories)) {
                    echo "<li>";
                    echo "ID: " . $category['id_category'] . " | ";
                    echo "URL: " . htmlspecialchars($category['url_category'] ?? 'NULL') . " | ";
                    echo "Title: " . htmlspecialchars($category['title_category'] ?? 'NULL');
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "❌ No categories found in table<br>";
            }
        } else {
            echo "❌ Query failed: " . mysqli_error($connection) . "<br>";
        }
    } else {
        echo "❌ Categories table does not exist<br>";
        
        // List all tables
        echo "<h4>Available tables:</h4><ul>";
        $tables = $connection->query("SHOW TABLES");
        while ($table = $tables->fetch_array()) {
            echo "<li>" . $table[0] . "</li>";
        }
        echo "</ul>";
    }
}

// Test 4: Check header.php categories query
echo "<h3>4. Header Query Test</h3>";
if ($connection && !$connection->connect_error) {
    $queryCategories = "SELECT url_category, title_category FROM categories ORDER BY title_category";
    $resultCategories = mysqli_query($connection, $queryCategories);
    
    if ($resultCategories && mysqli_num_rows($resultCategories) > 0) {
        echo "✅ Header query works. Found " . mysqli_num_rows($resultCategories) . " categories<br>";
    } else {
        echo "❌ Header query returns no results<br>";
        if (!$resultCategories) {
            echo "Error: " . mysqli_error($connection) . "<br>";
        }
    }
}

// Close connection
if (isset($connection)) {
    $connection->close();
}
?>