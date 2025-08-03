<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Direct News Test</h2>";

// Simulate the news URL parameter
$_GET['url_news'] = 'letnoe-uchilische-v-krasnodare-popolnitsya-15-buduschimi-voennyimi-letchitsami';

echo "<h3>Simulated GET parameters:</h3>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

// Test the news data fetch directly
echo "<h3>Testing news data fetch:</h3>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($connection->connect_error) {
        echo "Connection failed: " . $connection->connect_error . "<br>";
        exit();
    } else {
        echo "✅ Database connection successful<br>";
        $connection->set_charset("utf8mb4");
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "<br>";
    exit();
}

// Include and test the news data fetch
echo "<h4>Including news-data-fetch.php:</h4>";
try {
    include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-data-fetch.php';
    echo "✅ News data fetch included successfully<br>";
    
    if (isset($newsData)) {
        echo "✅ News data found:<br>";
        echo "Title: " . htmlspecialchars($newsData['title_news']) . "<br>";
        echo "ID: " . $newsData['id_news'] . "<br>";
        echo "Date: " . $newsData['date_news'] . "<br>";
    } else {
        echo "❌ No news data variable set<br>";
    }
    
    if (isset($pageTitle)) {
        echo "✅ Page title: " . htmlspecialchars($pageTitle) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error including news data fetch: " . $e->getMessage() . "<br>";
}

$connection->close();
?>